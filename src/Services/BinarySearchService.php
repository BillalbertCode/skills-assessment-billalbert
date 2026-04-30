<?php

namespace Dustov\Quotes\Services;

class BinarySearchService
{

    public function search(array $quotes, int $targetId): ?array
    {
        $low = 0;
        $high = count($quotes) - 1;

        while ($low <= $high) {
            $mid = (int) floor(($low + $high) / 2);
            $currentId = $quotes[$mid]['id'];

            if ($currentId == $targetId) {
                return $quotes[$mid];
            }

            if ($currentId < $targetId) {
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }
        return null;
    }

}