<?php

namespace App\Twig;

use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\Webspace\Analyzer\RequestAnalyzerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ServicesExtension extends AbstractExtension
{
    public function __construct(
        private DocumentManagerInterface $documentManager,
        private RequestAnalyzerInterface $requestAnalyzer
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sulu_services', [$this, 'getServices']),
        ];
    }

    public function getServices(): iterable
    {
        $webspaceKey = $this->requestAnalyzer->getWebspace()->getKey();
        $locale = $this->requestAnalyzer->getCurrentLocalization()->getLocale();

        $query = $this->documentManager->createQuery(
            'SELECT * FROM [nt:unstructured] AS a 
            WHERE a.[jcr:mixinTypes] = "sulu:page" 
            AND a.[i18n:' . $locale . '-template] = "service"
            AND ISDESCENDANTNODE(a, "/cmf/' . $webspaceKey . '/contents")
            ORDER BY a.[suluOrder] ASC',
            $locale
        );

        return $query->execute();
    }
}
