<?php
namespace App\Controller\Client;

use App\Entity\Association;
use App\Entity\AssociationRole;
use App\Entity\Client;
use App\Entity\ClientType;
use App\Repository\AssociationRepository;
use App\Repository\AssociationRoleRepository;
use App\Repository\ClientRepository;
use App\Repository\ClientTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function PHPUnit\Framework\equalTo;

class CommonClientMethods{
    /**
     * @param Client $client
     * @param Request $request
     * @param ClientTypeRepository $clientTypeRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @return void
     */
    public function setData(Client $client, Request $request, ClientTypeRepository $clientTypeRepository, UserPasswordHasherInterface $passwordHasher){
        $data = $request->request;

        $clientType = $data->get('clientType');
        $type = $clientTypeRepository->findOneBy(["name" => $clientType]);
        $isStudent = strcmp($clientType,"Etudiant") == 0;
        $assosRole = $data->get('assosRoles');
        $typeName = $isStudent?$clientType:$assosRole;
        $role = $this->getRoleFromType($typeName);
        /*
            if password input exists so it's the add page
            so we have to initialize the fidelity points
            and set the password to the chosen one.
        */
        if($data->get('password')){
            $hashedPassword=$passwordHasher->hashPassword($client,trim($data->get('password')));
            $client->setPassword($hashedPassword);
        }

        $client->setName(trim($data->get('name')));
        $client->setFirstName(trim($data->get('firstName')));
        $client->setLogin(trim($data->get('login')));
        $client->setBalance(trim($data->get('balance')));
        $client->setRoles($role);
        $client->setClientType($type);
    }

    private function getRoleFromType(string $typeName):array{
        $role=[];
        switch ($typeName){
            case "Tresorier":
                $role[] = "ROLE_TRESORIER";
                break;
            case "President":
                $role[] = "ROLE_PRESIDENT";
                break;
            case "Etudiant":
                $role[] = "ROLE_USER";
                break;
            default:
                $role[] = "ROLE_ASSOC";
                break;
        }
        return $role;
    }

    public function makeMember(Client $client, AssociationRole $role):Association
    {
        $newMember = new Association();

        $newMember->setMember($client);
        $newMember->setRole($role);

        return $newMember;
    }

    /**
     * Verify in the table Association if there is already a president
     * If there is one or more (which isn't possible, but it prevents bug)
     * It removes every President
     */
    public function removeOtherPresidents(EntityManagerInterface $manager, Association $associationMember,
        ClientTypeRepository $clientTypeRepository, ClientRepository $clientRepository, AssociationRepository $associationRepository)
    {
        $members = $associationRepository->findAll();

        foreach ($members as $otherMember){
            if($associationMember->getMember() !== $otherMember->getMember()){
                if($otherMember->getRole()->getName() == "President"){
                    $client = $clientRepository->findOneBy(['id'=> $otherMember->getMember()]);
                    $client->setClientType($clientTypeRepository->findOneBy(['name'=>'Etudiant']));
                    $client->setRoles(["ROLE_USER"]);
                    $manager->persist($client);
                    $manager->flush();
                }
            }
        }
    }
}