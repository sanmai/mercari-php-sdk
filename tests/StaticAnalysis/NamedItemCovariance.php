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

declare(strict_types=1);

namespace Tests\Mercari\StaticAnalysis;

use JMS\Serializer\Annotation\Type;
use Mercari\BrandsResponse;
use Mercari\CategoriesResponse;
use Mercari\DTO\Category;
use Mercari\DTO\NamedItem;
use Mercari\ListResponse;

/**
 * A static analyzer fixture.

 * @see NamedItem
 */
final class NamedItemCovariance
{
    /**
     * Every list of named items reads through the interface.
     *
     * @param ListResponse<NamedItem> $list
     *
     * @return iterable<int|string, string>
     */
    public function names(ListResponse $list): iterable
    {
        foreach ($list as $item) {
            yield $item->getId() => $item->getName();
        }
    }

    /**
     * @return iterable<int|string, string>
     */
    public function anyNamedList(CategoriesResponse $categories, BrandsResponse $brands): iterable
    {
        yield from $this->names($categories);

        yield from $this->names($brands);
    }

    /**
     * A response holds instances of any subclass of the item it declares.
     */
    public function translatedCategories(): CategoriesResponse
    {
        $response = new CategoriesResponse();
        $response->master_categories = [new TranslatedCategory()];

        return $response;
    }
}

final class TranslatedCategory extends Category
{
    public function getName(): string
    {
        return 'Video Games';
    }
}

/**
 * A response narrows the item it declares to a subclass of it.
 */
final class TranslatedCategoriesResponse extends CategoriesResponse
{
    /**
     * @var TranslatedCategory[]
     */
    #[Type('array<Tests\Mercari\StaticAnalysis\TranslatedCategory>')]
    public array $master_categories = [];
}
