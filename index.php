<?php
/**
 * 测试使用jwt包校验token
 */
require 'vendor/autoload.php';

use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint;

use function var_dump;

// $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NTg2OTYwNTIsIm5iZiI6MT'
//     . 'Y1ODY5NjA1MiwiZXhwIjoxNjU4Njk2NjUyLCJpc3MiOiJodHRwczovL2FwaS5teS1hd2Vzb'
//     . '21lLWFwcC5pbyIsImF1ZCI6Imh0dHBzOi8vY2xpZW50LWFwcC5pbyJ9.yzxpjyq8lXqMgaN'
//     . 'rMEOLUr7R0brvhwXx0gp56uWEIfc';


// $key = InMemory::base64Encoded(
//     'hiG8DlOKvtih6AxlZn5XKImZ06yu8I3mkOzaJrEuW8yAv8Jnkw330uMt8AEqQ5LB'
// );

// $token = (new JwtFacade())->parse(
//     $jwt,
//     new Constraint\SignedWith(new Sha256(), $key),
//     new Constraint\StrictValidAt(
//         new FrozenClock(new DateTimeImmutable('2022-07-24 20:55:10+00:00'))
//     )
// );


$jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJZWUNfVVNFUiIsInYiOjEsImJ1c2luZXNzVHlwZSI6InBhdGllbnQiLCJhY2NvdW50SWQiOjMyMzQwLCJhcHBWZXJzaW9uIjoiMC4xIiwic3lzVmVyc2lvbiI6IjAuMSIsInVzZXJuYW1lIjoiMTMwOTg4OTI0MjciLCJleHBpcmVUaW1lIjoxNjk0NzY1ODUyMzA1LCJyb2xlSWRzIjoiIiwiZGVwYXJ0bWVudElkIjowLCJwcm9qZWN0SWQiOjAsImNsaWVudCI6MCwib3BlbklkIjoib0FDZW41T1lwZnZMNmJfZGJPbkFQY0w0SEVoWSIsInVuaW9uSWQiOiJvYkp5TDZrUE9pMVNOSUhaOW5zSVVJaHVMRWRnIiwiaWRzbiI6IjUyMjQyODE5OTIxMDE0MDAyMSIsInRlbGVwaG9uZSI6IjEzMDk4ODkyNDI3IiwiY2xpZW50VHlwZSI6IndlY2hhdC1taW5pIiwiaWF0IjoxNjYzMjI5ODUyLCJjbGFpbXMiOnt9LCJleHAiOjE2OTQ3NjU4NTJ9.NzAqFH0-HVPkp7hwlwX0w60YZs9J1WJWnPDhehVUF5w';

$a_key = 'kSUdVKL0j0JGTAIo8uY5ZNMO9nZAemg6ehgOHozK';

// $key = hash_hmac('sha256', )

$key = InMemory::base64Encoded($a_key);

$token = (new JwtFacade())->parse(
    $jwt,
    new Constraint\SignedWith(new Sha256(), $key),
    new Constraint\StrictValidAt(
        Lcobucci\Clock\SystemClock::fromSystemTimezone()
        // new FrozenClock(new DateTimeImmutable('2022-07-24 20:55:10+00:00'))
    )
);

var_dump($token->claims()->all());



