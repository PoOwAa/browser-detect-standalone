<?php

namespace PoOwAa\BrowserDetect\Stages;

use DeviceDetector\Parser\Device\DeviceParserAbstract;
use League\Pipeline\StageInterface;
use PoOwAa\BrowserDetect\Contracts\PayloadInterface;

/**
 * Strong browser and platform detector.
 *
 * @package PoOwAa\BrowserDetect\Stages
 */
class DeviceDetector implements StageInterface
{
    /**
     * @param  PayloadInterface $payload
     * @return PayloadInterface
     */
    public function __invoke($payload)
    {
        // Skipping on bots, the detector is set to ignore bot details.
        if (!$payload->getValue('isBot')) {
            $detector = new \DeviceDetector\DeviceDetector();
            $detector->setUserAgent($payload->getAgent());
            $detector->skipBotDetection(true);
            $detector->parse();

            $platform = $detector->getOs();
            $browser = $detector->getClient();
            $device = [
                'type' => $detector->getDeviceName(),
                'brand' => $detector->getBrand(),
                'model' => $detector->getModel(),
            ];

            if ($platform !== null) {
                if (!empty($platform['name'])) {
                    $payload->setValue('platformFamily', $platform['name']);
                }

                if (!empty($platform['version'])) {
                    foreach ($this->parseVersion($platform['version'], 'platform') as $key => $value) {
                        $payload->setValue($key, $value);
                    }
                }
            }

            if ($browser !== null) {
                if (!empty($browser['name'])) {
                    $payload->setValue('browserFamily', $browser['name']);
                }

                if (!empty($browser['engine'])) {
                    $payload->setValue('browserEngine', $browser['engine']);
                }

                if (!empty($browser['version'])) {
                    foreach ($this->parseVersion($browser['version'], 'browser') as $key => $value) {
                        $payload->setValue($key, $value);
                    }
                }
            }

            if (!empty($device['type'])) {
                if ($device['type'] === 'desktop') {
                    $payload->setValue('isDesktop', true);
                } elseif ($device['type'] === 'tablet' || $device['type'] === 'phablet') {
                    $payload->setValue('isTablet', true);
                } elseif ($device['type'] === 'smartphone' || $device['type'] === 'feature phone') {
                    $payload->setValue('isMobile', true);
                }
            }

            if (!empty($device['brand'])) {
                $payload->setValue('deviceFamily', DeviceParserAbstract::getFullName($device['brand']));
            }

            if (!empty($device['model'])) {
                $payload->setValue('deviceModel', $device['model']);
            }
        }

        return $payload;
    }

    /**
     * Parse semantic version strings into major.minor.patch pieces.
     *
     * @param string $version
     * @param string $prefix
     * @return array
     */
    protected function parseVersion($version, $prefix)
    {
        $response = [];

        if (preg_match('%(?<major>\d+)((\.(?<minor>\d+)((\.(?<patch>\d+))|$))|$)%', $version, $match)) {
            $pieces = [];

            foreach ($match as $key => $value) {
                if ($key === 'major' || $key === 'minor' || $key === 'patch') {
                    $pieces[] = $response[$prefix . 'Version' . ucfirst($key)] = (int) $value;
                }
            }

            if (!empty($pieces)) {
                $response[$prefix . 'Version'] = implode('.', $pieces);
            }
        }

        return $response;
    }
}
