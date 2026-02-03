<?php

namespace Nadi\Yii2\Controllers;

use Nadi\Yii2\Nadi;
use Nadi\Yii2\Shipper\Shipper;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Nadi monitoring management commands.
 *
 * Register in your console application config:
 *
 * 'controllerMap' => [
 *     'nadi' => \Nadi\Yii2\Controllers\NadiController::class,
 * ],
 */
class NadiController extends Controller
{
    /**
     * Install and configure Nadi monitoring.
     */
    public function actionInstall(): int
    {
        $this->stdout("Installing Nadi Monitoring...\n");

        $configSource = dirname(__DIR__, 2).'/config/nadi.php';
        $configDest = \Yii::getAlias('@app').'/config/nadi.php';

        if (! file_exists($configDest)) {
            if (file_exists($configSource)) {
                copy($configSource, $configDest);
                $this->stdout("Configuration published to config/nadi.php\n");
            }
        } else {
            $this->stdout("Configuration already exists.\n");
        }

        // Install shipper
        $this->stdout("Installing Shipper Binary...\n");

        try {
            $shipper = new Shipper(\Yii::getAlias('@app'));
            $shipper->install();
            $this->stdout("Shipper binary installed successfully.\n");
        } catch (\Exception $e) {
            $this->stderr("Could not install shipper: {$e->getMessage()}\n");
        }

        $this->stdout("\nAdd to your .env:\n");
        $this->stdout("  NADI_API_KEY=your-api-key\n");
        $this->stdout("  NADI_APP_KEY=your-app-key\n");
        $this->stdout("\nNadi monitoring installed successfully!\n");

        return ExitCode::OK;
    }

    /**
     * Test the Nadi monitoring connection.
     */
    public function actionTest(): int
    {
        $this->stdout("Testing Nadi Connection...\n");

        $nadi = $this->getNadi();
        if (! $nadi) {
            $this->stderr("Nadi component is not configured.\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $transporter = $nadi->getTransporter();
        if (! $transporter) {
            $this->stderr("Nadi transporter is not configured.\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        try {
            $result = $transporter->test();
            if ($result) {
                $this->stdout("Successfully connected to Nadi!\n");

                return ExitCode::OK;
            }

            $this->stderr("Connection test failed.\n");

            return ExitCode::UNSPECIFIED_ERROR;
        } catch (\Exception $e) {
            $this->stderr("Connection test failed: {$e->getMessage()}\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Verify the Nadi monitoring configuration.
     */
    public function actionVerify(): int
    {
        $this->stdout("Verifying Nadi Configuration...\n");

        $nadi = $this->getNadi();
        if (! $nadi) {
            $this->stderr("Nadi component is not configured.\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $config = $nadi->getConfig();
        $this->stdout('Enabled: '.($config['enabled'] ? 'Yes' : 'No')."\n");
        $this->stdout("Driver: {$config['driver']}\n");

        $transporter = $nadi->getTransporter();
        if (! $transporter) {
            $this->stderr("Transporter could not be initialized.\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        try {
            $result = $transporter->verify();
            if ($result) {
                $this->stdout("Configuration verified successfully!\n");

                return ExitCode::OK;
            }

            $this->stderr("Verification failed.\n");

            return ExitCode::UNSPECIFIED_ERROR;
        } catch (\Exception $e) {
            $this->stderr("Verification error: {$e->getMessage()}\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Update the Nadi shipper binary.
     */
    public function actionUpdateShipper(): int
    {
        $this->stdout("Updating Nadi Shipper...\n");

        try {
            $shipper = new Shipper(\Yii::getAlias('@app'));
            $shipper->install();
            $this->stdout("Shipper binary updated successfully.\n");

            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("Failed to update shipper: {$e->getMessage()}\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    protected function getNadi(): ?Nadi
    {
        if (\Yii::$app->has('nadi')) {
            $component = \Yii::$app->get('nadi');
            if ($component instanceof \Nadi\Yii2\NadiComponent) {
                return $component->getNadi();
            }
        }

        return null;
    }
}
