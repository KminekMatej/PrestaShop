<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Builder\Map;

/**
 * This class is the representation of the last sheet of a translation catalogue : The message itself.
 * A message is composed by the default wording,
 * its translation within the project files (for crowdin or any translation tool),
 * its translation made in the BO interface and stored in DB.
 * If a message has file or user translation, it's considered as translated.
 */
class Message
{
    /**
     * @var string
     */
    private $defaultTranslation;

    /**
     * @var string|null
     */
    private $fileTranslation;

    /**
     * @var string|null
     */
    private $userTranslation;

    public function __construct(string $defaultTranslation)
    {
        $this->defaultTranslation = $defaultTranslation;
    }

    public function getKey(): string
    {
        return $this->defaultTranslation;
    }

    public function setFileTranslation(string $fileTranslation): self
    {
        $this->fileTranslation = $fileTranslation;

        return $this;
    }

    public function setUserTranslation(string $userTranslation): self
    {
        $this->userTranslation = $userTranslation;

        return $this;
    }

    /**
     * Returns whether a message is translated or not.
     * It's TRUE if one of fileTranslation or userTranslation is not null
     */
    public function isTranslated(): bool
    {
        return null !== $this->fileTranslation || null !== $this->userTranslation;
    }

    /**
     * Check if data contains search word.
     *
     * @param array $search
     *
     * @return bool
     */
    public function contains(array $search): bool
    {
        foreach ($search as $s) {
            $s = strtolower($s);
            if (
                false !== strpos(strtolower($this->defaultTranslation), $s)
                || (null !== $this->fileTranslation && false !== strpos(strtolower($this->fileTranslation), $s))
                || (null !== $this->userTranslation && false !== strpos(strtolower($this->userTranslation), $s))
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'default' => $this->defaultTranslation,
            'project' => $this->fileTranslation,
            'user' => $this->userTranslation,
        ];
    }
}
