<?php
class Reject_konsumen extends Controller {

    function Reject_konsumen()
    {
        parent::Controller();
        $this->load->model('Produk_model', 'produk');
        $this->load->model('Merek_model', 'merek');
        $this->load->model('Model_baju_model', 'model');
        $this->load->model('Reject_model', 'reject');
    }

    function index()
    {
        if($this->session->userdata('transaksi')!='reject_konsumen'){
            $this->cart->destroy();
            $this->session->set_userdata('transaksi', 'reject_konsumen');
        }
        $info = "";
        if ($this->input->post('submit')) {
            if(!$this->input->post('id')) $info = "Input produk tidak lengkap";
            else{
                $add = $this->add($this->input->post('id'), $this->input->post('jumlah'), $this->input->post('harga'));
                if (!$add) $info = "Stok Tidak mencukupi";
            }
        }
        $data = new stdClass();
        $data->info = $info;
        $data->view_konten = 'reject_konsumen';
        $data->title = "Reject Konsumen";
        $data->daftar_merek = $this->merek->get_semua_merek();
        $this->load->view('base', $data);
    }

    function refund(){
        $tanggal = date("Y-m-d H:i:s");
        $this->load->model('Order_model', 'order');

        // ====================Order====================
        $data = array(
            "tanggal"   => $tanggal,
            "total"     => $this->cart->total(),
            "jenis"     => "agen",
            "lunas"     => $this->session->userdata('pembayaran')
        );
        $id_order = $this->order->insert_order($data);

        //===================Reject=====================
        foreach($this->cart->contents() as $item){
            $this->reject->reject_konsumen($item['id'], $item['qty'], $item['price'], $id_order);
        }
        $this->cart->destroy();
        redirect('/reject_konsumen');
    }

    function batal(){
        $this->cart->destroy();
        redirect('/reject_konsumen');
    }

    function add($produk, $jumlah, $harga)
    {
        $produk = $this->produk->get_produk_by_id($produk);
        if($produk->stok<$jumlah){ return false;}
        else {
        $data = array(
            'id'    => $produk->id,
            'qty'   => $jumlah,
            'price' => $harga,
            'name'  => $produk->model,
            'merek' => $produk->merek,
            'warna' => $produk->warna,
            'ukuran'=> $produk->ukuran
        );
        $this->cart->insert($data);
        return true;
        }
    }

    function model($merek)
    {
        $x = $this->model->get_semua_model_by_merek($merek);
        $data = new stdClass();
        $data->daftar_model = $x;
        $this->load->view('ajax_model', $data);
    }

    function warna($model)
    {
        $x = $this->produk->get_semua_warna_by_model($model);
        $data = new stdClass();
        $data->daftar_warna = $x;
        $data->model_baju = $model;
        $this->load->view('ajax_warna', $data);
    }

    function ukuran($model, $warna)
    {
        $x = $this->produk->get_semua_ukuran($model, $warna);
        $data = new stdClass();
        $data->daftar_ukuran = $x;
        $data->model = $model;
        $data->warna = $warna;
        $this->load->view('ajax_ukuran', $data);
    }

}