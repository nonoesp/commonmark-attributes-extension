<?php

/*
 * This is part of the webuni/commonmark-attributes-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\AttributesExtension;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class AttributesBlockParser extends AbstractBlockParser
{
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        $document = $context->getDocument();
        $tip = $context->getTip();

        if (!$document->lastChild() instanceof AttributesDocument) {
            $attributesDocument = new AttributesDocument();
            $attributesDocument->replaceChildren($document->children());
            $document->replaceChildren([$attributesDocument]);

            if ($tip instanceof Document) {
                $context->setTip($attributesDocument);
            }
        }

        $state = $cursor->saveState();
        $attributes = AttributesUtils::parse($cursor);
        if (empty($attributes)) {
            return false;
        }

        if (null !== $cursor->getFirstNonSpaceCharacter()) {
            $cursor->restoreState($state);

            return false;
        }

        $context->addBlock(new Attributes($attributes));
        $context->setBlocksParsed(true);

        return true;
    }
}
