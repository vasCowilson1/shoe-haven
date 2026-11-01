<?php

namespace App\Console\Commands;

use App\Services\PesapalService;
use Illuminate\Console\Command;

class RegisterPesapalIpn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesapal:register-ipn {--url= : Optional custom IPN URL to register}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register your IPN URL with Pesapal API v3 and get your Notification ID';

    /**
     * Execute the console command.
     */
    public function handle(PesapalService $pesapal): int
    {
        $this->info('Connecting to Pesapal to register IPN endpoint...');

        $customUrl = $this->option('url');
        $ipnUrl = $customUrl ?: (config('pesapal.ipn_url') ?: route('pesapal.ipn'));

        $this->line("IPN URL: <comment>{$ipnUrl}</comment>");
        $this->line('Environment: <comment>' . config('pesapal.env', 'sandbox') . '</comment>');

        try {
            $ipnId = $pesapal->registerIpnUrl($ipnUrl);

            $this->newLine();
            $this->info('✓ IPN URL successfully registered with Pesapal!');
            $this->line("Generated IPN ID: <fg=green;options=bold>{$ipnId}</>");
            $this->newLine();
            $this->comment('Add this to your .env file:');
            $this->line("PESAPAL_IPN_ID={$ipnId}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to register IPN with Pesapal: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
