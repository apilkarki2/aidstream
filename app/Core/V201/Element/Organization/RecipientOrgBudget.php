<?php
namespace App\Core\V201\Element\Organization;

use App\Core\Elements\BaseElement;
use App;

class RecipientOrgBudget extends BaseElement
{
    protected $narratives = [];

    public function setNarrative($narrative)
    {
        $this->narratives[] = $narrative;

        return $this;
    }

    public function getForm()
    {
        return "App\Core\V201\Forms\Organization\MultipleRecipientOrgBudgetForm";
    }

    /**
     * @param $organization
     * @return mixed
     */
    public function getXmlData($organization)
    {
        $organizationData   = [];
        $recipientOrgBudget = (array) $organization->recipient_organization_budget;
        foreach ($recipientOrgBudget as $RecipientOrgBudget) {
            $organizationData[] = [
                'recipient-org' => [
                    '@attributes' => [
                        'ref' => $RecipientOrgBudget['recipientOrganization'][0]['Ref']
                    ],
                    'narrative' => $this->buildNarrative($RecipientOrgBudget['narrative'])
                ],
                'period-start' => [
                    '@attributes' => [
                        'iso-date' => $RecipientOrgBudget['periodStart'][0]['date']
                    ]
                ],
                'period-end' => [
                    '@attributes' => [
                        'iso-date' => $RecipientOrgBudget['periodEnd'][0]['date']
                    ]
                ],
                'value' => [
                    '@value'      => $RecipientOrgBudget['value'][0]['amount'],
                    '@attributes' => [
                        'currency' => $RecipientOrgBudget['value'][0]['currency'],
                        'value-date' => $RecipientOrgBudget['value'][0]['value_date']
                    ]
                ]
            ];
        }

        return $organizationData;
    }

    public function getRepository()
    {
        return App::make('App\Core\V201\Repositories\Organization\RecipientOrgBudgetRepository');
    }

}