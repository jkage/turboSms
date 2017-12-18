<?php
namespace App\Controller;


use App\Controller\AppController;
use Cake\Event\Event;
use AfricasTalkingGateway;

/**
 * Payments Controller
 *
 * @property \App\Model\Table\PaymentsTable $Payments
 */
class PaymentsController extends AppController
{
    
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $payments = $this->paginate($this->Payments);

        $this->set(compact('payments'));
        $this->set('_serialize', ['payments']);
    }

    /**
     * View method
     *
     * @param string|null $id Payment id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $payment = $this->Payments->get($id, [
            'contain' => []
        ]);

        $this->set('payment', $payment);
        $this->set('_serialize', ['payment']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $payment = $this->Payments->newEntity();
        if ($this->request->is('post')) {
            $payment = $this->Payments->patchEntity($payment, $this->request->data);
            if ($this->Payments->save($payment)) {
                $this->Flash->success(__('The payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The payment could not be saved. Please, try again.'));
        }
        $this->set(compact('payment'));
        $this->set('_serialize', ['payment']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Payment id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $payment = $this->Payments->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $payment = $this->Payments->patchEntity($payment, $this->request->data);
            if ($this->Payments->save($payment)) {
                $this->Flash->success(__('The payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The payment could not be saved. Please, try again.'));
        }
        $this->set(compact('payment'));
        $this->set('_serialize', ['payment']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Payment id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $payment = $this->Payments->get($id);
        if ($this->Payments->delete($payment)) {
            $this->Flash->success(__('The payment has been deleted.'));
        } else {
            $this->Flash->error(__('The payment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function send()
    {
        $payment = $this->Payments->newEntity();        
        if ($this->request->is('post')) {
            $payment = $this->Payments->patchEntity($payment, $this->request->data);
            //$url = "https://payments.sandbox.africastalking.com/mobile/b2c/request";
            $jdata = array(
                'username'=> 'sandbox',
                'productName'=> 'payroll',
                'recipients' => array(
                    array( 
                        "name"=> $this->request->data['recipient'],
                        "phoneNumber"=> $this->request->data['phone'],
                        "currencyCode"=> "UGX",
                        "amount"=>$this->request->data['amount'],
                        "reason"=> "SalaryPayment",
                        "metadata" => array(
                            "description" => "May Salary",
                            "employeeId" => "123"
                            )
                        )    
                    )
                );
            
            
            $result = $this->doAtPost(json_encode($jdata));
            
            $Fmessage = json_decode($result, true);
            if ($Fmessage['entries'][0]['status'] == 'Queued'){
                $this->Payments->save($payment);                
                $this->Flash->success(__('The CASH has been sent. TransactionID: '. $Fmessage['entries'][0]['transactionId']. '   THANK U :-)'));
                return $this->redirect(['action' => 'index']);
            }else {
                $this->Flash->error(__('The CASH not sent. Reason: '. $Fmessage['entries'][0]['errorMessage']));
            }
        }
        $this->set(compact('payment'));
        $this->set('_serialize', ['payment']);
    }
    
    private function doAtPost($post_data)
    {
        $url = "https://payments.sandbox.africastalking.com/mobile/b2c/request";
        
        $ch = curl_init();
        // *** Not secure, not a good idea for live environment *** //
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // ******************************************************* //
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, "username:password" );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apikey: 38a5d3364a2970ca7dc474ae85e551c6baf86c24b9d17e08b3944b946dc9101e',
            'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 
        
        $result = curl_exec($ch);
        
        // Check for errors
        if($result === FALSE){
            var_dump($result);
            die(curl_error($ch));
        }
        curl_close($ch);
        
        return $result;
        
    }

    public function send2()
    {
        require_once "AfricasTalkingGateway.php";

        $payment = $this->Payments->newEntity(); 

        if ($this->request->is('post')) {
            $payment = $this->Payments->patchEntity($payment, $this->request->data);
            //Specify your credentials
            $username = "sandbox";
            $apiKey   = "38a5d3364a2970ca7dc474ae85e551c6baf86c24b9d17e08b3944b946dc9101e";
            
            //Create an instance of our awesome gateway class and pass your credentials
            //$gateway = new AfricasTalkingGateway($username, $apiKey);

            $gateway = new AfricasTalkingGateway($username, $apiKey, "sandbox");
            
            /*************************************************************************************
            NOTE: If connecting to the sandbox:
            
            1. Use "sandbox" as the username
            2. Use the apiKey generated from your sandbox application
            https://account.africastalking.com/apps/sandbox/settings/key
            3. Add the "sandbox" flag to the constructor
            
            $gateway  = new AfricasTalkingGateway($username, $apiKey, "sandbox");
            **************************************************************************************/
            
            // Specify the name of your Africa's Talking payment product
            $productName  = "payroll";
            
            // The 3-Letter ISO currency code for the checkout amount
            $currencyCode = "UGX";
            

            $recipient1   = array("phoneNumber" => $this->request->data['phone'],
                "currencyCode" => $currencyCode,
                "amount"       => $this->request->data['amount'],
                "metadata"     => array("name"   => $this->request->data['recipient'],
                    "reason" => "May Salary")
                );
            /*// Provide the details of a mobile money recipient
            $recipient1   = array("phoneNumber" => "+254711345565",
                "currencyCode" => "UGX",
                "amount"       => 1050,
                "metadata"     => array("name"   => "Clerk",
                    "reason" => "May Salary")
                );
            // You can provide up to 10 recipients at a time
            $recipient2   = array("phoneNumber"  => "+254711890754",
                "currencyCode" => "UGX",
                "amount"       => 5010,
                "metadata"     => array("name"   => "Accountant",
                    "reason" => "May Salary")
                );*/

            // Put the recipients into an array
            //$recipients  = array($recipient1, $recipient2);
            $recipients  = array($recipient1);
            
            try {
                $responses = $gateway->mobilePaymentB2CRequest($productName, $recipients);
                
                foreach($responses as $response) {
                    //$Fmessage = json_decode($result, true);
                    if ($response->status == 'Queued'){
                        $this->Payments->save($payment);                
                        $this->Flash->success(__('The CASH has been sent. TransactionID: '.$response->transactionId. '   THANK U :-)'));
                        return $this->redirect(['action' => 'index']);
                    }else {
                        $this->Flash->error(__('The CASH not sent. Reason: '. $response->errorMessage. ':-('));
                    }
                    
                    // Parse the responses and print them out
                    /*echo "phoneNumber=".$response->phoneNumber;
                    echo ";status=".$response->status;
                    
                    if ($response->status == "Queued") {
                        echo ";transactionId=".$response->transactionId;
                        echo ";provider=".$response->provider;
                        echo ";providerChannel=".$response->providerChannel;
                        echo ";value=".$response->value;
                        echo ";transactionFee=".$response->transactionFee."\n";
                    } else {
                        echo ";errorMessage=".$response->errorMessage."\n";
                    }*/
                }
                
            }
            catch(AfricasTalkingGatewayException $e){
                $this->Flash->error(__('The CASH not sent. Reason: '. $e->getMessage()));
                //echo "Received error response: ".$e->getMessage();
            }
        }
        $this->set(compact('payment'));
        $this->set('_serialize', ['payment']);
    }

    public function filesend()
    {
        require_once "AfricasTalkingGateway.php";
        
        
        $payment = $this->Payments->newEntity();
        if($this->request->is('post')){
            $username = "sandbox";
            $apiKey   = "38a5d3364a2970ca7dc474ae85e551c6baf86c24b9d17e08b3944b946dc9101e";
            $gateway = new AfricasTalkingGateway($username, $apiKey, "sandbox");
            $productName  = "payroll";
            $currencyCode = "UGX";
            
            print_r($_FILES);
            if($_FILES['csv']){
                $filename = explode('.', $_FILES['csv']['name']);
                
                #debug($filename);
                if($filename[1]=='csv'){                    
                    $handle = fopen($_FILES['csv']['tmp_name'], "r");
                    print_r($handle);
                    print_r('##########');
                    while ($data = fgetcsv($handle)){
                        $item1 = $data[0];                        
                        $data = array(
                            'fieldName' => $item1
                            );
                        print_r('**********');
                        print_r($data);
                        //  $item2 = $data[1];
                        //  $item3 = $data[2];
                        //  $item4 = $data[3];
                        $payment = $this->Payments->newEntity($data);
                        $this->Payments->save($payment);
                        #$payment = $this->Payments->patchEntity($payment, $this->request->data);
                        #$Applicant = $this->Applicants->newEntity($data);
                        #$this->Applicants->save($Applicant);
                    }
                    fclose($handle);
                }
            }
        }
        //$this->render(FALSE);
        
        
        
        
        /*
        $recipient1   = array("phoneNumber" => $this->request->data['phone'],
            "currencyCode" => $currencyCode,
            "amount"       => $this->request->data['amount'],
            "metadata"     => array("name"   => $this->request->data['recipient'],
                "reason" => "May Salary")
            );
        
        // Put the recipients into an array
        //$recipients  = array($recipient1, $recipient2);
        $recipients  = array($recipient1);
        
        try {
            $responses = $gateway->mobilePaymentB2CRequest($productName, $recipients);
            
            foreach($responses as $response) {
                //$Fmessage = json_decode($result, true);
                if ($response->status == 'Queued'){
                    $this->Payments->save($payment);                
                    $this->Flash->success(__('The CASH has been sent. TransactionID: '.$response->transactionId. '   THANK U :-)'));
                    return $this->redirect(['action' => 'index']);
                }else {
                    $this->Flash->error(__('The CASH not sent. Reason: '. $response->errorMessage. ':-('));
                }
            }
            
        }
        catch(AfricasTalkingGatewayException $e){
            $this->Flash->error(__('The CASH not sent. Reason: '. $e->getMessage()));
            //echo "Received error response: ".$e->getMessage();
        }
        */
        $this->set(compact('payment'));
        $this->set('_serialize', ['payment']);
    }
}
