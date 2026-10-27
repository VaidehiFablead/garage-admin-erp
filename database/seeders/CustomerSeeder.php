<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get primary branch/admin ID if available
        $admin = User::where('role', 'admin')->first();
        $branchId = $admin ? $admin->id : 1;

        $customers = [
            [
                'name'         => 'Rajesh Sharma',
                'email'        => 'rajesh.sharma@example.com',
                'phone'        => '9825012341',
                'gst_num'      => '24ABCDE1234F1Z1',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'A-102, Gokul Residency, Ring Road',
                'city'         => 'Surat',
                'country'      => 'India',
            ],
            [
                'name'         => 'Amit Patel',
                'email'        => 'amit.patel@example.com',
                'phone'        => '9825012342',
                'gst_num'      => '24ABCDE1234F1Z2',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => '45, Shivalik Villa, SG Highway',
                'city'         => 'Ahmedabad',
                'country'      => 'India',
            ],
            [
                'name'         => 'Priya Mehta',
                'email'        => 'priya.mehta@example.com',
                'phone'        => '9825012343',
                'gst_num'      => null,
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'B-12, Green Park Society, Race Course Road',
                'city'         => 'Vadodara',
                'country'      => 'India',
            ],
            [
                'name'         => 'Vikram Singh',
                'email'        => 'vikram.singh@example.com',
                'phone'        => '9825012344',
                'gst_num'      => '24AABCS5678H1Z5',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'Shop 4, Royal Complex, Station Road',
                'city'         => 'Rajkot',
                'country'      => 'India',
            ],
            [
                'name'         => 'Sneha Desai',
                'email'        => 'sneha.desai@example.com',
                'phone'        => '9825012345',
                'gst_num'      => null,
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'Plot 88, Vasant Vihar, Bardoli Road',
                'city'         => 'Bardoli',
                'country'      => 'India',
            ],
            [
                'name'         => 'Kunal Verma',
                'email'        => 'kunal.verma@example.com',
                'phone'        => '9825012346',
                'gst_num'      => '24BCDEF9012J1Z8',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => '204, Riverview Apartment, Adajan',
                'city'         => 'Surat',
                'country'      => 'India',
            ],
            [
                'name'         => 'Jignesh Shah',
                'email'        => 'jignesh.shah@example.com',
                'phone'        => '9825012347',
                'gst_num'      => null,
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => '7, Kailash Nagar, Navrangpura',
                'city'         => 'Ahmedabad',
                'country'      => 'India',
            ],
            [
                'name'         => 'Bhavik Prajapati',
                'email'        => 'bhavik.prajapati@example.com',
                'phone'        => '9825012348',
                'gst_num'      => null,
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => '12, Shree Ram Society, Alkapuri',
                'city'         => 'Vadodara',
                'country'      => 'India',
            ],
            [
                'name'         => 'Rohit Chauhan',
                'email'        => 'rohit.chauhan@example.com',
                'phone'        => '9825012349',
                'gst_num'      => '24CDEFG3456K1Z9',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => '56, Udhna Main Road',
                'city'         => 'Surat',
                'country'      => 'India',
            ],
            [
                'name'         => 'Divya Joshi',
                'email'        => 'divya.joshi@example.com',
                'phone'        => '9825012350',
                'gst_num'      => null,
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'B-401, Harmony Heights, Kalawad Road',
                'city'         => 'Rajkot',
                'country'      => 'India',
            ],
        ];

        foreach ($customers as $data) {
            $user = User::updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'name'       => $data['name'],
                    'email'      => $data['email'],
                    'phone'      => $data['phone'],
                    'password'   => Hash::make('12345678'),
                    'role'       => 'customer',
                    'gst_num'    => $data['gst_num'],
                    'state_name' => $data['state_name'],
                    'state_code' => $data['state_code'],
                    'branch_id'  => $branchId,
                    'status'     => 1,
                    'isDeleted'  => 0,
                ]
            );

            UserDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'address'   => $data['address'],
                    'city'      => $data['city'],
                    'country'   => $data['country'],
                    'branch_id' => $branchId,
                    'isDeleted' => 0,
                ]
            );
        }
    }
}
