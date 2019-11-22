<?php
use Restserver\Libraries\REST_Controller;
class Prints extends REST_Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Authorization,Content-Type, ContentLength, Accept-Encoding");
        
        parent::__construct();
        $this
            ->load
            ->model('PrintsModel');
        $this
            ->load
            ->library('form_validation');
        $this->load->helper(['jwt', 'authorization']); 
    }

    public function index_get($id = null)
    {
        if ($id == null)
        {
            return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        else
        {
            $response = $this->PrintsModel->getAll($id);   
            return $this->response($response);
        }
    }

    public function index_post($id = null)
    {

        $data = 1;

        if($data)
        {
            $validation = $this->form_validation;
            $rule = $this
                ->PrintsModel
                ->rules();
            // if ($id == null)
            // {
            //     array_push($rule, ['field' => 'password', 'label' => 'password', 'rules' => 'required'], ['field' => 'email', 'label' => 'email', 'rules' => 'required|valid_email|is_unique[users.email]']);
            // }
            // else
            // {
            //     array_push($rule, ['field' => 'email', 'label' => 'email', 'rules' => 'required|valid_email']);
            // }

            // $validation->set_rules($rule);
            // if (!$validation->run())
            // {
            //     return $this->returnData($this
            //         ->form_validation
            //         ->error_array() , true);
            // }
            $user = new UserData();
            $user->filename = $this->post('filename');
            $user->link = $this->post('link');
            $user->tempat = $this->post('tempat');
            $user->status = $this->post('status');
            $user->harga = $this->post('harga');
            $user->id_pelanggan = $id;
            $response = $this
                ->PrintsModel
                ->store($user);
            return $this->returnData($response['msg'], $response['error']);
        }
        else
        {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            return $this->response($response);
        }
    }

    public function returnData($msg, $error)
    {
        $response['error'] = $error;
        $response['message'] = $msg;
        return $this->response($response);
    }

    public function verify_data()
    {
        // $cookie = $this->input->cookie('TOKEN',true);

        // if(!empty($cookie))
        // {
        //     $token = $cookie;
        // }
        // else
        // {
        //     return false;
        // }

        $headers = $this->input->request_headers();

        if(!empty($headers['Authorization']))
        {
            $token = $headers['Authorization'];
        }
        else
        {
            return false;
        }


        try {
        // Validate the token
        // Successfull validation will return the decoded user data else returns false
            $data = AUTHORIZATION::validateToken($token);
            $data2 = AUTHORIZATION::validateTimestamp($token);

            if ($data === false || $data2 === false) {
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                return false;
            } 
            else 
            {
                return $data;
            }
        }
        catch (Exception $e) 
        {
            // Token is invalid
            // Send the unathorized access message
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
            return false;
        }
    }
}
class UserData
{
    public $filename;
    public $link;
    public $tempat;
    public $status;
    public $id_pelanggan;
    public $harga;
}

