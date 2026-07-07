<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\ShippingLine;
use App\Models\Terminal;
use App\Models\Agency;
use App\Models\HsCode;
use App\Models\Insurer;
use App\Models\DocumentType;
use App\Models\ChargeItem;
use App\Models\ShipmentFile;
use App\Models\Consignment;
use App\Models\Container;
use App\Models\Declaration;
use App\Models\DeclarationItem;
use App\Models\Document;
use App\Models\Milestone;
use App\Models\DeliveryOrder;
use App\Models\Transporter;
use App\Models\Truck;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\FileCharge;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Receipt;
use App\Models\Disbursement;
use App\Models\PettyCash;
use App\Models\Expense;
use App\Models\InsurancePolicy;
use App\Models\InsuranceClaim;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $u = User::pluck('id','email');
        $adminId = $u['admin@caf.co.tz'] ?? 1;
        $opsId = $u['ops@caf.co.tz'] ?? 2;
        $declId = $u['declaration@caf.co.tz'] ?? 3;
        $clearId = $u['clearing@caf.co.tz'] ?? 4;
        $insOffId = $u['insurance@caf.co.tz'] ?? 9;

        $clients = $this->seedClients($adminId);
        [$transporters,$trucks,$drivers] = $this->seedTransport();
        $files = $this->seedShipmentFiles($clients,$opsId,$declId,$clearId);
        $this->seedConsignments($files);
        $this->seedDeclarations($files,$declId);
        $this->seedDocuments($files,$clearId,$declId);
        $this->seedMilestones($files,$clearId);
        $this->seedDeliveryOrders($files);
        $this->seedTrips($files,$trucks,$drivers);
        $this->seedCharges($files);
        $invoices = $this->seedInvoices($files,$adminId);
        $this->seedReceipts($invoices,$adminId);
        $this->seedDisbursements($files,$adminId);
        $this->seedPettyCash($adminId);
        $this->seedExpenses($adminId);
        $policies = $this->seedInsurance($files,$clients,$insOffId);
        $this->seedClaims($policies,$insOffId);
    }

    private function seedClients(int $adminId): array
    {
        $data = [
            ['CL-0001','Demo Trading Company Ltd','123-456-789','VRN-001','company','+255 754 111 111','client@demo.co.tz','Plot 1, Industrial Area, DSM',50000000,30],
            ['CL-0002','Kibo Gold Mining Ltd','222-333-444','VRN-002','company','+255 754 222 222','kibo@mining.co.tz','Plot 5, Mwanza Road, Mwanza',80000000,45],
            ['CL-0003','Simba Motors TZ','333-444-555','VRN-003','company','+255 754 333 333','simba@motors.co.tz','Plot 10, Nyerere Rd, DSM',30000000,15],
            ['CL-0004','Bamboo Imports LLC','444-555-666','VRN-004','company','+255 754 444 444','bamboo@imports.co.tz','Plot 22, Bagamoyo Rd, DSM',20000000,30],
            ['CL-0005','Highland Tea Company','555-666-777','VRN-005','company','+255 754 555 555','highland@tea.co.tz','Plot 8, Mbeya Road, Mbeya',15000000,30],
            ['CL-0006','Zanzibar Fisheries Co','666-777-888','VRN-006','company','+255 754 666 666','zfish@coast.co.tz','Plot 3, Malindi Rd, Zanzibar',10000000,14],
            ['CL-0007','Mwanza Construction Supply','777-888-999','VRN-007','company','+255 754 777 777','mcs@supply.co.tz','Plot 15, Cape Rd, Mwanza',40000000,30],
            ['CL-0008','Kilimanjaro Pharma Distributors','888-999-000','VRN-008','company','+255 754 888 888','kpharma@dist.co.tz','Plot 7, Moshi Rd, Arusha',60000000,45],
        ];
        $out = [];
        foreach ($data as $c) {
            $out[$c[0]] = Client::firstOrCreate(['code'=>$c[0]],[
                'name'=>$c[1],'tin'=>$c[2],'vrn'=>$c[3],'type'=>$c[4],
                'phone'=>$c[5],'email'=>$c[6],'address'=>$c[7],
                'credit_limit'=>$c[8],'payment_terms_days'=>$c[9],
                'is_active'=>true,'created_by'=>$adminId,
            ]);
        }
        return $out;
    }

    private function seedTransport(): array
    {
        $transporters = [];
        foreach ([['Trans African Logistics','+255 784 100 100','Juma Transport'],['Mwanza Haulage Co','+255 784 200 200','Ali Transport'],['Kibaha Freight Services','+255 784 300 300','Said Transport']] as $t) {
            $transporters[] = Transporter::firstOrCreate(['name'=>$t[0]],['phone'=>$t[1],'contact_person'=>$t[2],'is_active'=>true]);
        }
        $trucks = [];
        $td = [['T 123 ABC','TR-001',20,0],['T 456 DEF','TR-002',40,0],['T 789 GHI','TR-003',40,1],['T 321 JKL','TR-004',20,1],['T 654 MNO','TR-005',40,0],['T 987 PQR','TR-006',20,2],['T 147 STU','TR-007',40,2],['T 258 VWX','TR-008',40,0]];
        foreach ($td as $t) {
            $trucks[] = Truck::firstOrCreate(['plate_no'=>$t[0]],['trailer_no'=>$t[1],'capacity_tons'=>$t[2],'transporter_id'=>$transporters[$t[3]]->id ?? null,'is_active'=>true]);
        }
        $drivers = [];
        $dd = [['Hamisi Juma','+255 712 100 100','DL-001',0],['John Mrema','+255 712 200 200','DL-002',1],['Seif Abdallah','+255 712 300 300','DL-003',2],['Nuru Hassan','+255 712 400 400','DL-004',3],['Idd Mwita','+255 712 500 500','DL-005',4],['Frank Nyerere','+255 712 600 600','DL-006',5],['Omary Saidi','+255 712 700 700','DL-007',6],['Juma Komba','+255 712 800 800','DL-008',7]];
        foreach ($dd as $d) {
            $drivers[] = Driver::firstOrCreate(['name'=>$d[0]],['phone'=>$d[1],'license_no'=>$d[2],'truck_id'=>$trucks[$d[3]]->id ?? null,'is_active'=>true]);
        }
        return [$transporters,$trucks,$drivers];
    }

    private function seedShipmentFiles(array $clients, int $opsId, int $declId, int $clearId): array
    {
        $lines = ShippingLine::pluck('id','name');
        $terminals = Terminal::pluck('id','name');
        $fd = [
            ['CL-0001','import','sea','closed','BL-2024-001','MSC GULSUN','MSC','Shanghai','Dar es Salaam','Dar es Salaam Port','2024-01-10','2024-01-15','2024-01-22','USD',2500,'Electronics - Samsung TVs'],
            ['CL-0002','import','sea','invoiced','BL-2024-002','MAERSK SELETAN','Maersk','Dubai','Dar es Salaam','Dar es Salaam Port','2024-02-05','2024-02-10','2024-02-17','USD',2500,'Mining equipment'],
            ['CL-0003','import','sea','transport','BL-2024-003','CMA CGM JADE','CMA CGM','Yokohama','Dar es Salaam','Dar es Salaam Port','2024-03-01','2024-03-06','2024-03-13','USD',2500,'Vehicle parts 40ft'],
            ['CL-0004','import','sea','port_clearance','BL-2024-004','PIL KOTA','PIL','Ningbo','Dar es Salaam','Dar es Salaam Port','2024-03-15','2024-03-20','2024-03-27','USD',2500,'Furniture imports'],
            ['CL-0001','import','air','delivered','AWB-2024-005','EK783','Evergreen','Dubai','Dar es Salaam','Dar es Salaam Port','2024-02-20','2024-02-20','2024-02-27','USD',2500,'Air freight - pharmaceuticals'],
            ['CL-0005','export','sea','closed','BL-2024-006','MSC GULSUN','MSC','Dar es Salaam','Rotterdam','Dar es Salaam Port','2024-01-25','2024-01-25','2024-02-01','TZS',1,'Tea export 20ft'],
            ['CL-0006','import','sea','declaration','BL-2024-007','OOCL OAKLAND','OOCL','Mumbai','Dar es Salaam','Dar es Salaam Port','2024-03-20','2024-03-25','2024-04-01','USD',2500,'Fishing equipment'],
            ['CL-0007','import','sea','open','BL-2024-008','MAERSK KOTKA','Maersk','Istanbul','Dar es Salaam','Dar es Salaam Port','2024-03-28','2024-04-02','2024-04-09','USD',2500,'Construction materials'],
            ['CL-0008','import','air','open','AWB-2024-009','QR1346','Evergreen','Doha','Dar es Salaam','Dar es Salaam Port','2024-04-01','2024-04-01','2024-04-08','USD',2500,'Pharmaceutical bulk order'],
            ['CL-0002','transit','sea','transport','BL-2024-010','CMA CGM JADE','CMA CGM','Dubai','Dar es Salaam','Dar es Salaam Port','2024-03-10','2024-03-15','2024-03-22','USD',2500,'Transit to Zambia - copper'],
            ['CL-0003','import','sea','open','BL-2024-011','PIL KOTA','PIL','Guangzhou','Dar es Salaam','Dar es Salaam Port','2024-04-05','2024-04-10','2024-04-17','USD',2500,'Motor vehicle spare parts'],
            ['CL-0004','import','sea','draft','BL-2024-012','MSC GULSUN','MSC','Shenzhen','Dar es Salaam','Dar es Salaam Port','2024-04-08',null,null,'USD',2500,'Bamboo products - pending docs'],
        ];
        $files = [];
        foreach ($fd as $i => $f) {
            $fileNo = 'CAF/' . date('Y') . '/' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $files[] = ShipmentFile::firstOrCreate(['file_no'=>$fileNo],[
                'client_id'=>$clients[$f[0]]->id,'type'=>$f[1],'mode'=>$f[2],'status'=>$f[3],
                'bl_awb_no'=>$f[4],'vessel_flight'=>$f[5],'shipping_line_id'=>$lines[$f[6]] ?? null,
                'port_of_loading'=>$f[7],'port_of_discharge'=>$f[8],'terminal_id'=>$terminals[$f[9]] ?? null,
                'eta'=>$f[10],'discharge_date'=>$f[11],'free_days_expiry'=>$f[12],
                'currency'=>$f[13],'exchange_rate'=>$f[14],
                'assigned_declaration_officer_id'=>$declId,'assigned_clearing_officer_id'=>$clearId,
                'opened_by'=>$opsId,'opened_at'=>now()->subDays(60 - $i * 3),
                'closed_at'=>$f[3] === 'closed' ? now()->subDays(40 - $i * 3) : null,
                'remarks'=>$f[15],
            ]);
        }
        return $files;
    }

    private function seedConsignments(array $files): void
    {
        $cd = [
            ['Electronics - Samsung TVs and accessories',120,180,45000,'USD'],
            ['Mining drill bits and spare parts',45,120,85000,'USD'],
            ['Toyota vehicle parts and engines',80,200,120000,'USD'],
            ['Office furniture - desks and chairs',200,350,55000,'USD'],
            ['Pharmaceutical supplies - antibiotics',15,25,28000,'USD'],
            ['Highland tea - packed for export',350,280,42000,'USD'],
            ['Fishing nets and boat engines',60,90,38000,'USD'],
            ['Steel rods and cement',500,1200,95000,'USD'],
            ['Medical equipment - X-ray machines',25,40,180000,'USD'],
            ['Copper cathode sheets - transit to Zambia',400,600,320000,'USD'],
            ['Spare parts - Toyota and Nissan',100,180,75000,'USD'],
            ['Bamboo raw materials for crafts',300,250,18000,'USD'],
        ];
        $sizes = ['20ft','40ft','40hc'];
        foreach ($files as $i => $file) {
            $c = $cd[$i];
            $cons = Consignment::firstOrCreate(['shipment_file_id'=>$file->id],[
                'description'=>$c[0],'marks_numbers'=>'MARK-' . str_pad($i+1,3,'0',STR_PAD_LEFT),
                'packages_count'=>$c[1],'package_type'=>'Cartons',
                'gross_weight'=>$c[2],'volume_cbm'=>$c[3] * 0.01,
                'cargo_value'=>$c[3],'cargo_currency'=>$c[4],
            ]);
            $cnt = ($file->mode === 'air') ? 0 : rand(1, 3);
            for ($j = 0; $j < $cnt; $j++) {
                Container::firstOrCreate(['consignment_id'=>$cons->id,'container_no'=>'CONT-' . $file->id . '-' . ($j+1)],[
                    'size'=>$sizes[array_rand($sizes)],'seal_no'=>'SEAL-' . rand(10000,99999),
                    'status'=>$file->status === 'closed' ? 'empty_returned' : ($file->status === 'delivered' ? 'delivered' : 'at_port'),
                ]);
            }
        }
    }

    private function seedDeclarations(array $files, int $declId): void
    {
        $map = ['closed'=>'released','invoiced'=>'released','transport'=>'released','port_clearance'=>'assessed','delivered'=>'released','declaration'=>'lodged','open'=>'draft','draft'=>'draft'];
        $hs = HsCode::pluck('id')->take(3);
        foreach ($files as $file) {
            if ($file->status === 'draft') continue;
            $st = $map[$file->status] ?? 'draft';
            $duty = rand(500000, 5000000);
            $vat = $duty * 0.18;
            $total = $duty + $vat + rand(50000, 200000);
            $decl = Declaration::firstOrCreate(['shipment_file_id'=>$file->id],[
                'tansad_no'=>'TANSAD-' . str_pad($file->id, 6, '0', STR_PAD_LEFT),
                'regime_code'=>'IM4','declared_at'=>$file->opened_at?->addDays(2),
                'assessed_at'=>in_array($st,['assessed','released']) ? $file->opened_at?->addDays(4) : null,
                'duty_amount'=>$duty,'vat_amount'=>$vat,'other_taxes'=>rand(50000,200000),'total_taxes'=>$total,
                'status'=>$st,'officer_id'=>$declId,
            ]);
            foreach ($hs as $idx => $hsId) {
                $cv = rand(10000, 200000);
                $dr = rand(0, 25);
                DeclarationItem::firstOrCreate(['declaration_id'=>$decl->id,'hs_code_id'=>$hsId],[
                    'description'=>'Item ' . ($idx+1),'qty'=>rand(10,500),'customs_value'=>$cv,'duty_rate'=>$dr,'computed_duty'=>$cv*$dr/100,
                ]);
            }
        }
    }

    private function seedDocuments(array $files, int $clearId, int $declId): void
    {
        $dt = DocumentType::pluck('id','name');
        $names = ['Bill of Lading','Commercial Invoice','Packing List','TANSAD','Delivery Order','Proof of Delivery'];
        foreach ($files as $file) {
            if ($file->status === 'draft') continue;
            foreach ($names as $nm) {
                $id = $dt[$nm] ?? null;
                if (!$id) continue;
                if ($file->status === 'open' && in_array($nm,['TANSAD','Delivery Order','Proof of Delivery'])) continue;
                Document::firstOrCreate(['shipment_file_id'=>$file->id,'document_type_id'=>$id,'file_path'=>'docs/' . $file->file_no . '/' . strtolower(str_replace(' ','_',$nm)) . '.pdf'],[
                    'reference_no'=>'REF-' . rand(10000,99999),
                    'original_received'=>in_array($file->status,['closed','delivered']),
                    'received_at'=>$file->status !== 'open' ? now()->subDays(20) : null,
                    'uploaded_by'=>$clearId,
                    'verified_by'=>!in_array($file->status,['open','declaration']) ? $declId : null,
                    'verified_at'=>!in_array($file->status,['open','declaration']) ? now()->subDays(18) : null,
                ]);
            }
        }
    }

    private function seedMilestones(array $files, int $clearId): void
    {
        $map = [
            'draft'=>['file_opened'],'open'=>['file_opened','docs_complete'],
            'declaration'=>['file_opened','docs_complete','declaration_lodged'],
            'port_clearance'=>['file_opened','docs_complete','declaration_lodged','taxes_paid','customs_released'],
            'transport'=>['file_opened','docs_complete','declaration_lodged','taxes_paid','customs_released','do_collected','port_charges_paid','gate_out','insurance_arranged','in_transit'],
            'delivered'=>['file_opened','docs_complete','declaration_lodged','taxes_paid','customs_released','do_collected','port_charges_paid','gate_out','in_transit','delivered'],
            'invoiced'=>['file_opened','docs_complete','declaration_lodged','taxes_paid','customs_released','do_collected','port_charges_paid','gate_out','in_transit','delivered','empty_returned'],
            'closed'=>['file_opened','docs_complete','declaration_lodged','taxes_paid','customs_released','do_collected','port_charges_paid','gate_out','in_transit','delivered','empty_returned','file_closed'],
        ];
        foreach ($files as $file) {
            $stages = $map[$file->status] ?? ['file_opened'];
            $day = 0;
            foreach ($stages as $s) {
                Milestone::firstOrCreate(['shipment_file_id'=>$file->id,'stage'=>$s,'happened_at'=>$file->opened_at?->addDays($day++)],['recorded_by'=>$clearId]);
            }
        }
    }

    private function seedDeliveryOrders(array $files): void
    {
        foreach ($files as $file) {
            if (in_array($file->status,['transport','delivered','invoiced','closed'])) {
                DeliveryOrder::firstOrCreate(['shipment_file_id'=>$file->id],[
                    'do_no'=>'DO-' . str_pad($file->id,6,'0',STR_PAD_LEFT),
                    'shipping_line_id'=>$file->shipping_line_id,
                    'issued_at'=>$file->opened_at?->addDays(5),
                    'expiry_date'=>$file->opened_at?->addDays(12),
                    'deposit_amount'=>rand(500000,2000000),
                    'deposit_refunded'=>$file->status === 'closed',
                ]);
            }
        }
    }

    private function seedTrips(array $files, array $trucks, array $drivers): void
    {
        $map = ['transport'=>'in_transit','delivered'=>'delivered','invoiced'=>'closed','closed'=>'closed'];
        $dest = ['Mwanza','Arusha','Mbeya','Dodoma','Tunduma','Kigoma','Zambia - Lusaka','DRC - Lubumbashi'];
        foreach ($files as $i => $file) {
            if (!isset($map[$file->status])) continue;
            Trip::firstOrCreate(['shipment_file_id'=>$file->id,'truck_id'=>$trucks[$i % count($trucks)]->id,'driver_id'=>$drivers[$i % count($drivers)]->id],[
                'origin'=>'Dar es Salaam Port','destination'=>$dest[$i % count($dest)],
                'dispatched_at'=>$file->opened_at?->addDays(8),
                'delivered_at'=>in_array($map[$file->status],['delivered','closed']) ? $file->opened_at?->addDays(12) : null,
                'status'=>$map[$file->status],'transport_cost'=>rand(800000,3000000),
            ]);
        }
    }

    private function seedCharges(array $files): void
    {
        $ch = ChargeItem::pluck('id','name');
        $names = ['Agency Fee'=>150000,'Transport'=>null,'Documentation Fee'=>50000,'Port Charges'=>null,'TRA Duty'=>null,'VAT'=>null];
        foreach ($files as $file) {
            if ($file->status === 'draft') continue;
            foreach ($names as $nm => $default) {
                $id = $ch[$nm] ?? null;
                if (!$id) continue;
                $amt = $default ?? rand(200000, 3000000);
                FileCharge::firstOrCreate(['shipment_file_id'=>$file->id,'charge_item_id'=>$id],[
                    'description'=>$nm . ' for ' . $file->file_no,'amount'=>$amt,'currency'=>$file->currency,'is_billable'=>true,
                ]);
            }
        }
    }

    private function seedInvoices(array $files, int $adminId): array
    {
        $inv = [];
        $cnt = 0;
        foreach ($files as $file) {
            if (!in_array($file->status,['invoiced','closed'])) continue;
            $cnt++;
            $no = 'INV/' . date('Y') . '/' . str_pad($cnt,4,'0',STR_PAD_LEFT);
            $sub = rand(1500000,6000000);
            $vat = $sub * 0.18;
            $total = $sub + $vat;
            $paid = $file->status === 'closed' ? $total : $total * 0.5;
            $i = Invoice::firstOrCreate(['invoice_no'=>$no],[
                'shipment_file_id'=>$file->id,'client_id'=>$file->client_id,
                'date'=>$file->opened_at?->addDays(15),'due_date'=>$file->opened_at?->addDays(45),
                'currency'=>'TZS','exchange_rate'=>1,'subtotal'=>$sub,'vat_amount'=>$vat,'total'=>$total,
                'amount_paid'=>$paid,'status'=>$file->status === 'closed' ? 'paid' : 'partially_paid',
                'kind'=>'invoice','created_by'=>$adminId,
            ]);
            $inv[] = $i;
            $ch = ChargeItem::pluck('id','name');
            foreach (['Agency Fee'=>150000,'Documentation Fee'=>50000] as $nm => $price) {
                $cId = $ch[$nm] ?? null;
                if ($cId) InvoiceItem::firstOrCreate(['invoice_id'=>$i->id,'charge_item_id'=>$cId],['description'=>$nm,'qty'=>1,'unit_price'=>$price,'vat_rate'=>18,'line_total'=>$price*1.18]);
            }
        }
        return $inv;
    }

    private function seedReceipts(array $invoices, int $adminId): void
    {
        $cnt = 0;
        foreach ($invoices as $inv) {
            if ($inv->amount_paid > 0) {
                $cnt++;
                Receipt::firstOrCreate(['invoice_id'=>$inv->id,'amount'=>$inv->amount_paid],[
                    'receipt_no'=>'RCT/' . date('Y') . '/' . str_pad($cnt,4,'0',STR_PAD_LEFT),
                    'client_id'=>$inv->client_id,'date'=>$inv->date?->addDays(5),
                    'method'=>'bank','reference'=>'PAY-' . rand(10000,99999),'received_by'=>$adminId,
                ]);
            }
        }
    }

    private function seedDisbursements(array $files, int $adminId): void
    {
        $ag = Agency::pluck('id','name');
        foreach ($files as $file) {
            if (!in_array($file->status,['invoiced','closed','transport','delivered'])) continue;
            for ($d = 0; $d < rand(1,3); $d++) {
                $an = ['TRA','TBS','Tanzania Ports Authority'][array_rand(['TRA','TBS','Tanzania Ports Authority'])];
                Disbursement::firstOrCreate(['shipment_file_id'=>$file->id,'agency_id'=>$ag[$an] ?? null,'description'=>'Disbursement ' . ($d+1) . ' for ' . $file->file_no],[
                    'payee'=>$an,'amount'=>rand(100000,1500000),'currency'=>'TZS',
                    'paid_at'=>$file->opened_at?->addDays(3+$d),'method'=>'bank',
                    'reference_no'=>'DB-' . rand(10000,99999),'recorded_by'=>$adminId,'approved_by'=>$adminId,
                ]);
            }
        }
    }

    private function seedPettyCash(int $adminId): void
    {
        $descs = ['Fuel for field visit','Lunch for port team','Photocopy charges at TRA','Transport to ICD','Courier charges for docs'];
        for ($p = 0; $p < 10; $p++) {
            PettyCash::firstOrCreate(['date'=>now()->subDays($p*2)->format('Y-m-d'),'description'=>$descs[$p%5]],[
                'amount'=>rand(10000,80000),'type'=>'out','recorded_by'=>$adminId,'approved_by'=>$adminId,
            ]);
        }
    }

    private function seedExpenses(int $adminId): void
    {
        $cats = ['Office Supplies','Utilities','Fuel','Internet & Phone','Staff Welfare','Repairs'];
        for ($e = 0; $e < 8; $e++) {
            Expense::firstOrCreate(['date'=>now()->subDays($e*3)->format('Y-m-d'),'description'=>$cats[$e%6] . ' expense'],[
                'category'=>$cats[$e%6],'amount'=>rand(50000,300000),'recorded_by'=>$adminId,
            ]);
        }
    }

    private function seedInsurance(array $files, array $clients, int $insOffId): array
    {
        $ins = Insurer::pluck('id','name');
        $insNames = ['Jubilee Insurance','NIC Insurance','AAR Insurance','Resolution Insurance','Heritage Insurance'];
        $types = ['marine_cargo','goods_in_transit','marine_cargo','goods_in_transit','warehouse'];
        $statuses = ['active','active','active','expired','active','claimed','quoted','active'];
        $cargo = [
            ['Electronics - Samsung TVs',45000,'USD'],['Mining drill bits',85000,'USD'],
            ['Toyota vehicle parts',120000,'USD'],['Office furniture',55000,'USD'],
            ['Pharmaceutical supplies',28000,'USD'],['Highland tea export',42000,'USD'],
            ['Fishing equipment',38000,'USD'],['Construction materials',95000,'USD'],
        ];
        $policies = [];
        foreach ($files as $i => $file) {
            if ($file->status === 'draft' || $i >= 8) continue;
            $no = 'POL/' . date('Y') . '/' . str_pad($i+1,4,'0',STR_PAD_LEFT);
            $prem = rand(500000,3000000);
            $pol = InsurancePolicy::firstOrCreate(['policy_no'=>$no],[
                'client_id'=>$file->client_id,'shipment_file_id'=>$file->id,
                'insurer_id'=>$ins[$insNames[$i%5]] ?? null,
                'type'=>$types[$i%5],'status'=>$statuses[$i%8],
                'cover_start_date'=>$file->opened_at?->addDay(),
                'cover_end_date'=>$file->opened_at?->addMonths(3),
                'origin'=>$file->port_of_loading ?? 'Dar es Salaam',
                'destination'=>$file->port_of_discharge ?? 'Mwanza',
                'cargo_description'=>$cargo[$i][0] ?? 'General cargo',
                'cargo_value'=>$cargo[$i][1] ?? 50000,'cargo_currency'=>$cargo[$i][2] ?? 'USD',
                'sum_insured'=>($cargo[$i][1] ?? 50000) * 1.1,
                'premium_amount'=>$prem,'premium_currency'=>'TZS',
                'commission_rate'=>15,'commission_amount'=>$prem*0.15,
                'terms_conditions'=>'Standard marine cargo terms. All risks covered.',
                'issued_by'=>$insOffId,'issued_at'=>$file->opened_at?->addDay(),
            ]);
            $policies[] = $pol;
        }
        // Standalone policies
        $ck = array_keys($clients);
        for ($s = 0; $s < 3; $s++) {
            $no = 'POL/' . date('Y') . '/' . str_pad(count($policies)+$s+1,4,'0',STR_PAD_LEFT);
            $prem = rand(300000,1500000);
            $pol = InsurancePolicy::firstOrCreate(['policy_no'=>$no],[
                'client_id'=>$clients[$ck[$s%count($ck)]]->id,'shipment_file_id'=>null,
                'insurer_id'=>$ins[$insNames[$s]] ?? null,
                'type'=>$types[$s],'status'=>$statuses[$s+2],
                'cover_start_date'=>now()->subMonths(2-$s)->startOfMonth(),
                'cover_end_date'=>now()->addMonths(10+$s)->endOfMonth(),
                'origin'=>'Dar es Salaam','destination'=>'Various',
                'cargo_description'=>'Warehouse stock - general goods',
                'cargo_value'=>rand(50000,200000),'cargo_currency'=>'USD',
                'sum_insured'=>rand(100000,500000),
                'premium_amount'=>$prem,'premium_currency'=>'TZS',
                'commission_rate'=>15,'commission_amount'=>$prem*0.15,
                'terms_conditions'=>'Standard warehouse cover. Fire and theft included.',
                'issued_by'=>$insOffId,'issued_at'=>now()->subMonths(2-$s),
            ]);
            $policies[] = $pol;
        }
        return $policies;
    }

    private function seedClaims(array $policies, int $insOffId): void
    {
        $st = ['submitted','under_review','approved','settled','rejected'];
        $desc = ['Container damaged during transit - water ingress','Theft of cargo at overnight parking in Tunduma','Fire damage to warehouse stock','Breakage of fragile items during loading','Short delivery - 5 packages missing on arrival'];
        $loc = ['Dar es Salaam Port','Tunduma Border Post','Warehouse in Mwanza','ICD Isaka','Dar es Salaam Port'];
        for ($c = 0; $c < 5; $c++) {
            $pol = $policies[$c] ?? $policies[0];
            $no = 'CLM/' . date('Y') . '/' . str_pad($c+1,4,'0',STR_PAD_LEFT);
            $claimed = rand(500000,5000000);
            $appr = in_array($st[$c],['approved','settled']) ? $claimed*0.8 : 0;
            $setl = $st[$c] === 'settled' ? $appr : 0;
            InsuranceClaim::firstOrCreate(['claim_no'=>$no],[
                'insurance_policy_id'=>$pol->id,'client_id'=>$pol->client_id,
                'shipment_file_id'=>$pol->shipment_file_id,'status'=>$st[$c],
                'incident_date'=>now()->subDays(30-$c*5),
                'incident_description'=>$desc[$c],'loss_location'=>$loc[$c],
                'claimed_amount'=>$claimed,'approved_amount'=>$appr,'settled_amount'=>$setl,
                'submitted_at'=>now()->subDays(28-$c*5),
                'settled_at'=>$st[$c] === 'settled' ? now()->subDays(10) : null,
                'insurer_response'=>$st[$c] === 'rejected' ? 'Claim denied - outside policy coverage' : ($st[$c] === 'settled' ? 'Claim settled in full' : null),
                'handled_by'=>$insOffId,
            ]);
        }
    }
}
