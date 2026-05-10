<?php
namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use App\Entity\Security\User;
use App\Repository\UserRepository;

class ApiUserProvider implements UserProviderInterface {

    protected $user;
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @deprecated This method is deprecated and should not be used.
     */
    function loadUserByUsername($apiKey) {
        throw new \BadMethodCallException('loadUserByUsername is deprecated. Use loadUserByIdentifier instead.');
    }

    /**
     * Loads the user for the given user identifier (e.g., username or email).
     *
     * @param string $identifier The user identifier
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['apiKey' => $identifier]);

        if (empty($user)) {
            throw new UsernameNotFoundException('Could not find user with identifier: ' . $identifier);
        }

        return $user;
    }

    /**
     * @throws UnsupportedUserException if the account is not supported
     *
     * @return UserInterface
     */
    function refreshUser(UserInterface $user) {
        return $user;
    }

    /**
     * @param string $class
     *
     * @return Boolean
     */
    function supportsClass($class) {
        return $class === \App\Entity\Security\User::class;
    }
}
