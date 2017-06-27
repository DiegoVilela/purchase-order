<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Account;
use AppBundle\Entity\Balance;
use AppBundle\Entity\PurchaseOrder;
use AppBundle\Entity\Supplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class responsible for update balances
 *
 * @author Diego Vilela <vilelaphp@gmail.com>
 */
class BalancesManager
{
    const ACCOUNTS_WITH_BALANCE_TO_BE_SETTLED = [
        '622920101',
        '622920102',
        '631100000',
        '631200000'
    ];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Content of the text file with the balances to be updated.
     *
     * @var array
     */
    private $fileArray;

    /**
     * @var Account
     */
    private $account;

    /**
     * @var Supplier[]
     */
    private $suppliersList;

    /**
     * @var PurchaseOrder[]
     */
    private $purchaseOrdersList;

    /**
     * @var Balance[]
     */
    private $balancesList;

    /**
     * Array with the ids of Purchase Orders in the text file.
     *
     * @var array
     */
    private $purchaseOrdersWithBalanceIds;

    /**
     * Array with the ids of Purchase Orders without balance.
     *
     * @var array
     */
    private $purchaseOrdersWithoutBalanceIds;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->suppliersList = $this->getSuppliers();
        $this->purchaseOrdersList = $this->getPurchaseOrders();
    }

    public function processFile(UploadedFile $txtFile): void
    {
        $this->fileArray = $this->txtToArray($txtFile);
        $this->account = $this->getAccount();
        $this->balancesList = $this->getBalances();

        $this->updateBalances();

        $this->deleteBalances();

        $this->settlePurchaseOrders();
    }

    public function exportAccount(Account $account)
    {
        /** @var Balance[] $balances */
        $balances = $this->em->getRepository(Balance::class)
            ->findAllByAccount($account->getId());

        $file = 'downloads'.DIRECTORY_SEPARATOR.$account->getNumber().'.csv';
        $fp = fopen($file, 'w');
        foreach ($balances as $balance) {
            $row = [
                $balance->getPurchaseOrder()->getNumber(),
                $balance->getPurchaseOrder()->getSupplier()->getName(),
                $balance->getAmount()
            ];

            fputcsv($fp, $row);
        }
        fclose($fp);

        $response = new BinaryFileResponse($file);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $account->getNumber().'.csv'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }

    /**
     * @throws UploadException
     * @throws FileException
     */
    private function txtToArray(UploadedFile $txtFile): array
    {
        if ($txtFile->getError() > 0) {
            throw new UploadException($txtFile->getErrorMessage());
        }

        if ($txtFile->getMimeType() !== 'text/plain') {
            throw new UploadException(sprintf('The type of the file must be "text/plain". "%s" given.', $txtFile->getMimeType()));
        }

        $fileArray = file($txtFile);
        if ( ! $fileArray) {
            throw new FileException(sprintf('The file %s could not be read.', $txtFile->getFilename()));
        }

        return $fileArray;
    }

    private function updateBalances(): void
    {
        $i = 19;
        while ($i < count($this->fileArray)) {

            $purchaseOrderNumber = $this->getPurchaseOrderNumber($i);

            if ( ! empty($purchaseOrderNumber)) {

                $balanceAmount = $this->getBalanceAmount($i + 1);
                $supplier = $this->getSupplier($this->getSupplierName($i + 1));
                $purchaseOrder = $this->getPurchaseOrder($purchaseOrderNumber, $supplier, $balanceAmount);

                $idFake = (string) $this->account->getId() . '-' . $purchaseOrder->getId();

                $this->updateBalance($balanceAmount, $purchaseOrder, $idFake);
            }

            $i += 2;
        }

        $this->em->flush();
    }

    /**
     * @return integer Número da account
     */
    private function getAccountNumero()
    {
        return (integer) substr($this->fileArray[6], 18, 9);
    }

    /**
     * @return string Nome da account
     */
    private function getAccountNome()
    {
        return trim(substr($this->fileArray[17], 29, 43));
    }

    /**
     * @throws Exception
     */
    private function getAccount(): Account
    {
        $number = $this->getAccountNumero();
        $name = $this->getAccountNome();

        $account = $this->em->getRepository('AppBundle:Account')
            ->findOneBy(['number' => $number]);

        if (!$account) {
            $account = new Account();
            $account->setNumber($number);
            $account->setName($name);

            $errors = $this->validator->validate($account);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                throw new Exception($errorsString);
            }

            $this->em->persist($account);
            $this->em->flush();
        }

        return $account;
    }

    /**
     * @return Supplier[]
     */
    private function getSuppliers(): array
    {
        $suppliers = $this->em->getRepository('AppBundle:Supplier')->findAll();

        $suppliersList = [];
        for ($i = 0; $i < count($suppliers); $i++) {
            $suppliersList[$suppliers[$i]->getName()] = $suppliers[$i];
        }

        return $suppliersList;
    }

    /**
     * @return PurchaseOrder[]
     */
    private function getPurchaseOrders(): array
    {
        $purchaseOrders = $this->em->getRepository('AppBundle:PurchaseOrder')->findAll();

        $purchaseOrdersList = [];
        foreach ($purchaseOrders as $purchaseOrder) {
            $purchaseOrdersList[$purchaseOrder->getNumber()] = $purchaseOrder;
        }

        return $purchaseOrdersList;
    }

    /**
     * @return Balance[]
     */
    private function getBalances(): array
    {
        /** @var Balance[] $balances */
        $balances = $this->em->getRepository('AppBundle:Balance')->findBy(['account' => $this->account]);

        $balancesList = [];
        foreach ($balances as $balance) {
            $balanceId = (string) $balance->getAccount()->getId() . '-' . $balance->getPurchaseOrder()->getId();
            $balancesList[$balanceId] = $balance;
        }

        return $balancesList;
    }

    /**
     * Delete the balances of Purchase Orders that are not in the text file anymore.
     */
    private function deleteBalances(): void
    {
        /** @var Balance[] $balances */
        $balances = $this->em->getRepository('AppBundle:Balance')
            ->findBy(['account' => $this->account]);

        // TODO array_diff
        foreach ($balances as $balance) {
            if ( ! in_array($balance->getPurchaseOrder()->getId(), $this->purchaseOrdersWithBalanceIds)) {
                $this->purchaseOrdersWithoutBalanceIds[] = $balance->getPurchaseOrder()->getId();
            }
        }

        if ( ! empty($this->purchaseOrdersWithoutBalanceIds)) {
            $qb = $this->em->createQueryBuilder();

            $and = $qb->expr()->andX();
            $and->add($qb->expr()->in('b.purchaseOrder', $this->purchaseOrdersWithoutBalanceIds));
            $and->add($qb->expr()->eq('b.account', $this->account->getId()));

            $qb->delete('AppBundle:Balance', 'b')
                ->where($and);

            $qb->getQuery()->execute();
        }

        $this->purchaseOrdersWithBalanceIds = null;
    }

    private function getPurchaseOrderNumber(int $row): ?string
    {
        return trim(substr($this->fileArray[$row], 2, 12));
    }

    private function getSupplierName(int $row): ?string
    {
        return trim(substr($this->fileArray[$row], 0, 51));
    }

    /**
     * @throws Exception
     */
    private function getBalanceAmount(int $row): ?float
    {
        $amount = trim(substr($this->fileArray[$row], 60, 14));
        $amount = str_replace(',', '', $amount);

        if ( ! is_numeric($amount) OR empty($amount)) {
            throw new Exception('Invalid Balance.amount');
        }

        return $amount;
    }

    private function getSupplier(string $name): Supplier
    {
        if (array_key_exists($name, $this->suppliersList)) {
            return $this->suppliersList[$name];
        } else {
            return $this->getNewSupplier($name);
        }
    }

    private function getPurchaseOrder(string $number, Supplier $supplier, float $balanceAmount): PurchaseOrder
    {
        if (array_key_exists($number, $this->purchaseOrdersList)) {
            return $this->purchaseOrdersList[$number];
        } else {
            return $this->getNewPurchaseOrder($number, $supplier, $balanceAmount);
        }
    }

    private function updateBalance(float $amount, PurchaseOrder $purchaseOrder, string $idFake): void
    {
        if (array_key_exists($idFake, $this->balancesList)) {
            $balance = $this->balancesList[$idFake];

            if ($balance->getAmount() != $amount) {
                $balance->setAmount($amount);
                $this->em->persist($balance);
            }
        } else {
            $this->insertNewBalance($amount, $purchaseOrder, $idFake);
        }

        $this->purchaseOrdersWithBalanceIds[] = $purchaseOrder->getId();
    }

    private function settlePurchaseOrders(): void
    {
        $settledPurchaseOrders = $this->getSettledPurchaseOrders();

        foreach ($settledPurchaseOrders as $purchaseOrder) {
            $purchaseOrder->settle();
            $this->em->persist($purchaseOrder);
        }

        $this->em->flush();
    }

    /**
     * @return array|PurchaseOrder[]
     */
    private function getSettledPurchaseOrders(): ?array
    {
        $allPurchaseOrders = $this->em->getRepository(PurchaseOrder::class)
            ->findAllWithBalances();

        /** @var PurchaseOrder $purchaseOrder */
        foreach ($allPurchaseOrders as $purchaseOrder) {
            /** @var Balance $balance */
            foreach ($purchaseOrder->getBalances() as $balance) {
                if (in_array($balance->getAccount()->getNumber(),
                    self::ACCOUNTS_WITH_BALANCE_TO_BE_SETTLED)) {
                    $purchaseOrdersNotSettled[] = $purchaseOrder;
                    break;
                }
            }
        }

        if (!empty($purchaseOrdersNotSettled)) {
            return array_diff($allPurchaseOrders, $purchaseOrdersNotSettled);
        }

        return null;
    }

    private function insertNewBalance(float $amount, PurchaseOrder $purchaseOrder, string $idFake): void
    {
        $balance = new Balance($amount, $this->account, $purchaseOrder);
        $purchaseOrder->addBalance($balance);

        $this->em->persist($balance);
        $this->em->persist($purchaseOrder);
        $this->em->flush();

        $this->balancesList[$idFake] = $balance;
    }

    /**
     * @throws Exception
     */
    private function getNewSupplier(string $name): Supplier
    {
        $supplier = new Supplier($name);

        $errors = $this->validator->validate($supplier);
        if (count($errors) > 0) {
            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                throw new Exception($errorsString);
            }
        }

        $this->em->persist($supplier);
        $this->em->flush();

        $this->suppliersList[$supplier->getName()] = $supplier;

        return $supplier;
    }

    private function getNewPurchaseOrder(string $number, Supplier $supplier, float $balanceAmount): PurchaseOrder
    {
        $purchaseOrder = new PurchaseOrder($number, $supplier);
        $balance   = new Balance($balanceAmount, $this->account, $purchaseOrder);
        $purchaseOrder->addBalance($balance);

        $this->em->persist($purchaseOrder);
        $this->em->persist($balance);
        $this->em->flush();

        $this->purchaseOrdersList[$purchaseOrder->getNumber()] = $purchaseOrder;
        $balanceId = (string) $this->account->getId() . '-' . $purchaseOrder->getId();
        $this->balancesList[$balanceId] = $balance;

        return $purchaseOrder;
    }
}
