<?php 
class ControllerLocalisationPostType extends Controller {
	public function getPostTypes() {
		$json = array();
        $this->load->model('localisation/post_type');

        $post_types = $this->model_localisation_post_type->getPostTypes();
        foreach ($post_types as $post_type) {
            // if($post_type['post_type_id'] == 12) {
            //     continue;
            // }
            $json[] = array(
                'post_type_id'      => $post_type['post_type_id'],
                'name'              => $post_type['name'] 
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
	}

    public function getPriceSerice() {
        $this->load->model('localisation/post_type');

        if(isset($this->request->post['post_type_id'])) {
            $post_type_id = $this->request->post['post_type_id'];
        } else {
            $post_type_id = 0;
        }

        if(isset($this->request->post['date_post_id'])) {
            $date_post_id = $this->request->post['date_post_id'];
        } else {
            $date_post_id = 0;
        }

        if(isset($this->request->post['from_date'])) {
            $from_date = $this->request->post['from_date'];
        } else {
            $from_date = '';
        }

        $to_date    = '';

        if($from_date) {
            $to_date    =  date('d/m/Y', strtotime($from_date. ' + '.(int)$date_post_id.' days'));
        }

        $from_date    =  date('d/m/Y', strtotime($from_date));

        $json = array();
        $post_type = $this->model_localisation_post_type->getPostTypeById($post_type_id);

        
            $price_service          = $post_type ? $post_type['price'] : 0;
            $price_vat              = $this->tax->calculate($price_service * $date_post_id, 9) - ($price_service * $date_post_id);
            $json = array(
                'price_service'     => $this->currency->format_default($price_service, $this->config->get('config_currency')),
                'price_vat'         => $this->currency->format_default($price_vat, $this->config->get('config_currency')),
                'price_finnal'      => $this->currency->format_default($this->tax->calculate($price_service * $date_post_id, 9), $this->config->get('config_currency')),
                'status'            => TRUE,
                'date_post'         => $date_post_id.' Ng??y',
                'from_date'         => $from_date,
                'to_date'           => $to_date,
            );
       
       
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }

    public function getDatePosts() {
        $json = array();
       
      
        $json = array(
            array(
                'date_post_id'      => 7,
                'name'              => '7 Ng??y',
            ),
            array(
                'date_post_id'      => 8,
                'name'              => '8 Ng??y',
            ),
            array(
                'date_post_id'      => 9,
                'name'              => '9 Ng??y',
            ),
            array(
                'date_post_id'      => 10,
                'name'              => '10 Ng??y',
            ),
            array(
                'date_post_id'      => 11,
                'name'              => '11 Ng??y',
            ),
            array(
                'date_post_id'      => 12,
                'name'              => '12 Ng??y',
            ),
            array(
                'date_post_id'      => 13,
                'name'              => '13 Ng??y',
            ),
            array(
                'date_post_id'      => 14,
                'name'              => '14 Ng??y',
            ),
            array(
                'date_post_id'      => 15,
                'name'              => '15 Ng??y',
            ),
            array(
                'date_post_id'      => 16,
                'name'              => '16 Ng??y',
            ),
            array(
                'date_post_id'      => 17,
                'name'              => '17 Ng??y',
            ),
            array(
                'date_post_id'      => 18,
                'name'              => '18 Ng??y',
            ),
            array(
                'date_post_id'      => 19,
                'name'              => '19 Ng??y',
            ),
            array(
                'date_post_id'      => 20,
                'name'              => '20 Ng??y',
            ),
            array(
                'date_post_id'      => 21,
                'name'              => '21 Ng??y',
            ),
            array(
                'date_post_id'      => 22,
                'name'              => '22 Ng??y',
            ),
            array(
                'date_post_id'      => 23,
                'name'              => '23 Ng??y',
            ),
            array(
                'date_post_id'      => 24,
                'name'              => '24 Ng??y',
            ),
            array(
                'date_post_id'      => 25,
                'name'              => '25 Ng??y',
            ),
            array(
                'date_post_id'      => 26,
                'name'              => '26 Ng??y',
            ),
            array(
                'date_post_id'      => 27,
                'name'              => '27 Ng??y',
            ),
            array(
                'date_post_id'      => 28,
                'name'              => '28 Ng??y',
            ),
            array(
                'date_post_id'      => 29,
                'name'              => '29 Ng??y',
            ),
            array(
                'date_post_id'      => 30,
                'name'              => '30 Ng??y',
            ),
            array(
                'date_post_id'      => 31,
                'name'              => '31 Ng??y',
            ),
            array(
                'date_post_id'      => 90,
                'name'              => '90 Ng??y',
            ),
            array(
                'date_post_id'      => 180,
                'name'              => '180 Ng??y',
            )
        );
      
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}