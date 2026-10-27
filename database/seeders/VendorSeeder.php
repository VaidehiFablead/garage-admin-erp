<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get primary branch/admin ID if available
        $admin = User::where('role', 'admin')->first();
        $branchId = $admin ? $admin->id : 1;

        $vendors = [
            [
                'name'         => 'Bosch Automotive Spares',
                'email'        => 'sales@boschauto.example.com',
                'phone'        => '9879011001',
                'gst_num'      => '24AABCB1234D1Z1',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'Plot 101, GIDC Industrial Estate, Sachin',
                'city'         => 'Surat',
                'country'      => 'India',
            ],
            [
                'name'         => 'Castrol Lubricants & Oils',
                'email'        => 'orders@castroloil.example.com',
                'phone'        => '9879011002',
                'gst_num'      => '24AABCC2345E1Z2',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'Warehouse 12, Transport Nagar, Narol',
                'city'         => 'Ahmedabad',
                'country'      => 'India',
            ],
            [
                'name'         => 'Exide Batteries Distribution',
                'email'        => 'support@exidebattery.example.com',
                'phone'        => '9879011003',
                'gst_num'      => '24AABCD3456F1Z3',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'Shop 25, Auto Market, Sayajigunj',
                'city'         => 'Vadodara',
                'country'      => 'India',
            ],
            [
                'name'         => 'MRF & Apollo Tyres Hub',
                'email'        => 'contact@apollotyreshub.example.com',
                'phone'        => '9879011004',
                'gst_num'      => '24AABCE4567G1Z4',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'Opp. ST Workshop, Gondal Road',
                'city'         => 'Rajkot',
                'country'      => 'India',
            ],
            [
                'name'         => 'Minda Auto Components Ltd',
                'email'        => 'info@mindaauto.example.com',
                'phone'        => '9879011005',
                'gst_num'      => '24AABCF5678H1Z5',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'B-14, Phase 2, GIDC Vatva',
                'city'         => 'Ahmedabad',
                'country'      => 'India',
            ],
            [
                'name'         => 'Uno Minda Electricals',
                'email'        => 'sales@unominda.example.com',
                'phone'        => '9879011006',
                'gst_num'      => '24AABCG6789I1Z6',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'Ring Road Auto Complex',
                'city'         => 'Surat',
                'country'      => 'India',
            ],
            [
                'name'         => 'Mahle Filters & Engine Parts',
                'email'        => 'orders@mahleparts.example.com',
                'phone'        => '9879011007',
                'gst_num'      => '24AABCH7890J1Z7',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'Plot 78, Makarpura GIDC',
                'city'         => 'Vadodara',
                'country'      => 'India',
            ],
            [
                'name'         => 'Brembo Brake Systems Gujarat',
                'email'        => 'brembo.guj@example.com',
                'phone'        => '9879011008',
                'gst_num'      => '24AABCI8901K1Z8',
                'state_name'   => 'Gujarat',
                'state_code'   => '24',
                'address'      => 'Metoda GIDC, Lodhika',
                'city'         => 'Rajkot',
                'country'      => 'India',
            ],
        ];

        foreach ($vendors as $data) {
            $user = User::updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'name'       => $data['name'],
                    'email'      => $data['email'],
                    'phone'      => $data['phone'],
                    'password'   => Hash::make('12345678'),
                    'role'       => 'vendor',
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
