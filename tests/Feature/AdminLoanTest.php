<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Loan;

class AdminLoanTest extends TestCase
{
    public function testLoanListedSuccessfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $this->json('GET', 'api/admin/loan/list', ['Accept' => 'application/json'])
            ->assertStatus(200);
    }

    public function testApproveLoan()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $loanData = Loan::first();

        $this->json('GET', 'api/admin/loan/approve/' . $loanData->id, ['Accept' => 'application/json'])
            ->assertStatus(200);
    }
}
