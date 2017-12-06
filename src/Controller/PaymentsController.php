<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Messages Controller
 *
 * @property \App\Model\Table\MessagesTable $Messages
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
        $messages = $this->paginate($this->Messages);

        $this->set(compact('messages'));
        $this->set('_serialize', ['messages']);
    }

    /**
     * View method
     *
     * @param string|null $id Message id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $message = $this->Messages->get($id, [
            'contain' => []
        ]);

        $this->set('message', $message);
        $this->set('_serialize', ['message']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $message = $this->Messages->newEntity();
        if ($this->request->is('post')) {
            $message = $this->Messages->patchEntity($message, $this->request->data);
            if ($this->Messages->save($message)) {
                $this->Flash->success(__('The message has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The message could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Message id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $message = $this->Messages->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $message = $this->Messages->patchEntity($message, $this->request->data);
            if ($this->Messages->save($message)) {
                $this->Flash->success(__('The message has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The message could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Message id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $message = $this->Messages->get($id);
        if ($this->Messages->delete($message)) {
            $this->Flash->success(__('The message has been deleted.'));
        } else {
            $this->Flash->error(__('The message could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	/**
     * Send method
     *
     */
/*    public function send()
    {
        $message = $this->Messages->newEntity();
        if ($this->request->is('post')) {
			$message = $this->Messages->patchEntity($message, $this->request->data);
			//print_r($message['content']);
			$url = "https://vusion.texttochange.org/story1/programUnattachedMessages/add.json";
			$post_data = array(
				'send-to-type' => 'phone',
				'content' => $message['content'],
				'send-to-phone[0]' => $message['phone'],
				'type-schedule' => $message['schedule'],
			);
		
			$ch = curl_init();
			// *** Not secure, not a good idea for live environment *** //
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			// ******************************************************* //
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, "username:password" );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
			$result = curl_exec($ch);
			
			// Check for errors
			if($result === FALSE){
				var_dump($result);
				die(curl_error($ch));
			}
			curl_close($ch);
			
			
			
			$this->Flash->success(__('The SMS has been sent.'));
        }
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }
*/
    public function send()
    {
        $message = $this->Messages->newEntity();
        //print_r($this->request->data);
        //print_r('**************');
        if ($this->request->is('post')) {
           // print_r($this->request->data);
            //$message = $this->Messages->patchEntity($message, $this->request->data);
            //print_r($message['content']);
            $url = "https://payments.africastalking.com/mobile/b2c/request";
            $jdata = array(
                'username'=> 'pmaxmass',
                'productName'=> 'B2C Payment',
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
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('apikey: 05742127f9e183ffbcf6c43842f7ae91bfb07cd6e528c67a43778797d0d7af58',
             'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);            

            $result = curl_exec($ch);

            //print_r($result);
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
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }
}

