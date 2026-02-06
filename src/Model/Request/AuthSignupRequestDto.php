<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AuthSignupRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'First Name is required!')]
        #[Assert\Length(
            min: 3,
            max: 50,
            minMessage: 'First Name must be at least {{ limit }} characters long!',
            maxMessage: 'First Name must be at most {{ limit }} characters long!'
        )]
        #[SerializedName("first_name")]
        public string $firstName,

        #[Assert\NotBlank(message: 'Last Name is required!')]
        #[Assert\Length(
            min: 3,
            max: 50,
            minMessage: 'Last Name must be at least {{ limit }} characters long!',
            maxMessage: 'Last Name must be at most {{ limit }} characters long!'
        )]
        #[SerializedName("last_name")]
        public string $lastName,

        #[Assert\NotBlank(message: 'Email is required!')]
        #[Assert\Email(message: 'Email is not valid!')]
        public string $email,

        #[Assert\NotBlank(message: 'Phone Number is required!')]
        #[SerializedName("phone_number")]
        public string $phoneNumber,

        #[Assert\NotBlank(message: 'Password is required!')]
        public string $password,

        #[Assert\NotBlank(message: 'Bank is required!')]
        #[SerializedName("bank_code")]
        public string $bankCode,

        #[Assert\NotBlank(message: 'Account Number is required!')]
        #[SerializedName("account_number")]
        public string $accountNumber,

        #[Assert\NotBlank(message: 'Account Name is required!')]
        #[SerializedName("account_name")]
        public string $accountName,

        #[Assert\NotBlank(message: "Device UID is required.")]
        #[SerializedName("device_uid")]
        public string $deviceUid,
    ) {}
}