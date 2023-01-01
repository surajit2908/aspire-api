<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Loan;

class CustomerLoanTest extends TestCase
{
    public function testAddLoan()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $loanData = [
            "amount" => 10,
            "term" => 3
        ];

        $this->json('POST', 'api/customer/loan/add', $loanData, ['Accept' => 'application/json'])
            ->assertStatus(200);
    }

    public function testLoanListedSuccessfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $this->json('GET', 'api/customer/loan/list', ['Accept' => 'application/json'])
            ->assertStatus(200);
    }
}
