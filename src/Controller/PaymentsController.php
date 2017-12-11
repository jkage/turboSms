<?php
namespace App\Controller;

use App\Controller\AppController;

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
        //print_r($this->request->data);
        //print_r('**************');
        if ($this->request->is('post')) {
           // print_r($this->request->data);
            //$message = $this->Messages->patchEntity($message, $this->request->data);
            //print_r($message['content']);
            $url = "https://payments.sandbox.africastalking.com/mobile/b2c/request";
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

            $post_data = json_encode($jdata);
            
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

            print_r($result);
            // Check for errors
            if($result === FALSE){
                var_dump($result);
                die(curl_error($ch));
            }
            curl_close($ch);
            $Fmessage = json_decode($result, true);
            //print_r('**********');
            //print_r($Fmessage['entries'][0]['transactionId']);
            $this->Flash->success(__('The CASH has been sent. TransactionID: '. $Fmessage['entries'][0]['transactionId']. '   THANK U :-)'));
        }
        $this->set(compact('payment'));
        $this->set('_serialize', ['payment']);
    }
}
