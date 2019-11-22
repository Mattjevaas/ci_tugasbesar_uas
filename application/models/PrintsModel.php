<?php
defined('BASEPATH') or exit('No direct script access allowed');
class PrintsModel extends CI_Model
{
    private $table = 'prints';
    public $id;
    public $filename;
    public $link;
    public $tempat;
    public $status;
    public $harga;
    public $id_pelanggan;
    public $created_at;

    public $rule = [['field' => 'name', 'label' => 'name', 'rules' => 'required'], ];

    public function Rules()
    {
        return $this->rule;
    }

    public function getAll($id)
    {
        $row = $this->db->select('*')->get_where($this->table, array('id_pelanggan' => $id))->result();
        
        if(!empty($row))
        {
            $response = ['data' => $row];
                
            return $response;
        }
        else
        {
            return ['msg' => 'User tidak ditemukan!','error' => true];
        }
    }

    public function store($request)
    {
        $this->filename = $request->filename;
        $this->link = $request->link;
        $this->tempat = $request->tempat;
        $this->harga = $request->harga;
        $this->status = $request->status;
        $this->id_pelanggan = $request->id_pelanggan;

        if ($this
            ->db
            ->insert($this->table, $this))
        {
            return ['msg' => 'Berhasil', 'error' => false];
        }
        return ['msg' => 'Gagal', 'error' => true];
    }

}
?>
