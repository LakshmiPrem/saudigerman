<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use DocuSign\eSign\Configuration;
use DocuSign\eSign\ApiClient;
use DocuSign\eSign\Api\EnvelopesApi;

class DocuSign_lib {
    protected $CI;
    protected $apiClient;

    public function __construct() {
      //  $this->CI =& get_instance();
       // $this->CI->load->config('docusign');

        $this->apiClient = new ApiClient();
        $this->apiClient->setBasePath(get_option('docusign_base_path'));
        $this->apiClient->getOAuth()->setOAuthBasePath('account-d.docusign.com');
        $this->apiClient->getOAuth()->setAccessToken($this->CI->config->item('docusign_access_token'));
    }

    public function createEnvelope($file, $signerName, $signerEmail) {
        $envelopeApi = new EnvelopesApi($this->apiClient);

        // Prepare the envelope
        $envelopeDefinition = new \DocuSign\eSign\Model\EnvelopeDefinition();
        $envelopeDefinition->setEmailSubject('Please sign this document');
        $envelopeDefinition->setDocuments([
            (new \DocuSign\eSign\Model\Document())
                ->setDocumentBase64(base64_encode(file_get_contents($file)))
                ->setName('DocumentName.pdf')
                ->setDocumentId('1')
        ]);

        $signer = new \DocuSign\eSign\Model\Signer();
        $signer->setEmail($signerEmail);
        $signer->setName($signerName);
        $signer->setRecipientId("1");
        $signer->setTabs($tabs);

        $recipients = new \DocuSign\eSign\Model\Recipients();
        $recipients->setSigners([$signer]);
        $envelopeDefinition->setRecipients($recipients);
        $envelopeDefinition->setStatus("sent");

        // Create and send the envelope
        $envelopeSummary = $envelopeApi->createEnvelope($this->CI->config->item('docusign_account_id'), $envelopeDefinition);
        return $envelopeSummary;
    }
}
?>