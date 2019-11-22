<?php
defined('BASEPATH') or exit('No direct script access allowed');
class UserModel extends CI_Model
{
    private $table = 'users';
    public $id;
    public $name;
    public $email;
    public $password;
    public $alamat;
    public $no_telp;
    public $stat_admin;
    public $photo;
    public $rule = [['field' => 'name', 'label' => 'name', 'rules' => 'required'], ];
    
    public function Rules()
    {
        return $this->rule;
    }

    public function getbyID($id)
    {
        $row = $this->db->select('*')->get_where($this->table, array('id' => $id),1,0)->row();
        
        if(!empty($row))
        {
            $row2 = ['id'=> $row->id , 'name' => $row->name ,'email'=> $row->email, 'alamat'=> $row->alamat, 'no_telp'=> $row->no_telp, 'stat_admin'=> $row->stat_admin,'photo' => $row->photo];
            $response = ['data' => $row2];
                
            return $response;
        }
        else
        {
            return ['msg' => 'User tidak ditemukan!','error' => true];
        }
    
    }

    public function user_login($request)
    {
        $this->email = $request->email;
        $this->password = $request->password;

        $row = $this->db->select('*')->get_where($this->table, array('email' => $this->email),1,0)->row();
        
        if(!empty($row))
        {
            if(password_verify($this->password, $row->password))
            {
                $timestamp = now();
                $token = AUTHORIZATION::generateToken(['email' => $this->email,'password'=> $row->password,'timestamp' => $timestamp]);
                $row2 = ['id'=> $row->id , 'name' => $row->name ,'email'=> $row->email, 'alamat'=> $row->alamat, 'no_telp'=> $row->no_telp, 'stat_admin'=> $row->stat_admin];
                $response = ['data' => $row2, 'token' => $token];
                
                return $response;
            }
            else
            {
                return ['msg' => 'Invalid username or password!','error' => true];
            }
        }
        else{
            return ['msg' => 'User tidak ditemukan!','error' => true];
        }
    }

    public function store($request)
    {
        $this->name = $request->name;
        $this->email = $request->email;
        $this->alamat = $request->alamat;
        $this->no_telp = $request->no_telp;
        $this->stat_admin = 3;

        $this->password = password_hash($request->password, PASSWORD_BCRYPT);

        if ($this
            ->db
            ->insert($this->table, $this))
        {
            return ['msg' => 'Berhasil'];
        }
        return ['msg' => 'Gagal', 'error' => true];
    }

    public function update($request, $id)
    {
        $updateData = ['name' => $request->name, 'alamat' => $request->alamat, 'no_telp' => $request->no_telp];
        if ($this
            ->db
            ->where('id', $id)->update($this->table, $updateData))
        {
            return ['msg' => 'Berhasil', 'error' => false];
        }
        return ['msg' => 'Gagal', 'error' => true];
    }

    public function do_upload($request,$id)
    {

        $config['upload_path']          = './upload/';
        $config['allowed_types']        = 'pdf|png|gif|jpg|jpeg|rar|zip';
        //$config['file_name']            = $post["namamakul"];
        $config['overwrite']			= true;
        // $config['max_size']             = 1024; // 1MB
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('file'))
        {
            $error = array('error' => $this->upload->display_errors());
            return ['msg' => 'Gagal Upload!', 'error' => true];
        }
        else
        {
            $updateData = ['photo' => 'http://localhost/ci_tugasbesar_uas/upload/'.$this->upload->data('file_name')];
            
            $this->db->where('id', $id)->update($this->table, $updateData);

            $data = array('upload_data' => $this->upload->data());
            return ['msg' => 'Berhasil Upload!', 'error' => false]; 
        }

    }
    
    public function destroy($id)
    {
        if (empty($this
            ->db
            ->select('*')
            ->where(array(
            'id' => $id
        ))->get($this->table)
            ->row())) return ['msg' => 'Id tidak ditemukan', 'error' => true];

        if ($this
            ->db
            ->delete($this->table, array(
            'id' => $id
        )))
        {
            return ['msg' => 'Berhasil', 'error' => false];
        }
        
        return $this->returnData($response['msg'], $response['error']);
    }
}

?>
