<?php
/**
 * Mercari PHP SDK
 * Copyright 2024 Alexey Kopytko
 *
 * Mercari PHP SDK is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 2 of the License, or (at your option) any later version.
 *
 * Mercari PHP SDK is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Mercari PHP SDK. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Mercari;

use JMS\Serializer\Annotation\Type;
use Mercari\DTO\Comment;
use ArrayIterator;
use IteratorAggregate;
use ReturnTypeWillChange;

/**
 * @template-implements IteratorAggregate<Comment>
 */
class CommentsResponse extends ListResponse
{
    /**
     * @var Comment[]
     * @Type("array<Mercari\DTO\Comment>")
     */
    public array $comments = [];

    /**
     * @return ArrayIterator<array-key, Comment>
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->comments);
    }

}
