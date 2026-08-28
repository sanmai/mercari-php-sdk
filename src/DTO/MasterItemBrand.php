<?php

/**
 * Mercari PHP SDK
 * Copyright 2024 Alexey Kopytko <alexey@kopytko.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mercari\DTO;

class MasterItemBrand implements NamedItem
{
    public string $id;

    public string $name_ja;

    public string $name_en;

    public bool $item_auth_supported;

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * The Japanese name. Replace this method to give the name in another language.
     */
    public function getName(): string
    {
        return $this->name_ja;
    }
}
