<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Paybill;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EncryptPaybillKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example: php artisan paybills:encrypt
     */
    protected $signature = 'paybills:encrypt';

    /**
     * The console command description.
     */
    protected $description = 'Encrypt plain-text consumer_key, consumer_secret, and passkey for all Paybills';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting paybill encryption...");

        $paybills = Paybill::all();
        $count = 0;

        foreach ($paybills as $paybill) {
            $updated = false;

            // Consumer Key
            if ($paybill->consumer_key) {
                if (!$this->isEncrypted($paybill->consumer_key)) {
                    $paybill->consumer_key = Crypt::encryptString($paybill->consumer_key);
                    $updated = true;
                }
            }

            // Consumer Secret
            if ($paybill->consumer_secret) {
                if (!$this->isEncrypted($paybill->consumer_secret)) {
                    $paybill->consumer_secret = Crypt::encryptString($paybill->consumer_secret);
                    $updated = true;
                }
            }

            // Passkey
            if ($paybill->passkey) {
                if (!$this->isEncrypted($paybill->passkey)) {
                    $paybill->passkey = Crypt::encryptString($paybill->passkey);
                    $updated = true;
                }
            }

            if ($updated) {
                $paybill->save();
                $count++;
            }
        }

        $this->info("Encryption completed. {$count} paybills updated.");
        return 0;
    }

    /**
     * Check if a string is already encrypted
     */
    private function isEncrypted($value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (DecryptException $e) {
            return false;
        }
    }
}
