<?php
class Retur extends Model {

    function Retur(){
        parent::Model();
        $this->load->model('Produk', 'produk');
        $this->load->model('Agen', 'agen');
    }

    function retur_konsumen($id_produk, $jumlah, $harga)
    {
        $x = $this->produk->get_produk_by_id($id_produk);
        #produk,tanggal,jumlah,harga,agen,refund
        $tanggal = date("Y-m-d H:i:s");
        $refund = ($harga - $x->harga_beli) * $jumlah;
        $data = array(
            "tanggal" => $tanggal,
            "agen" => 0,
            "produk" => $id_produk,
            "jumlah" => $jumlah,
            "harga" => $harga,
            "refund" => $refund
        );
        $q = $this->db->insert('transaksi_retur',$data);
        if($q){
            return $this->produk->tambah_stok_produk($id_produk,$jumlah,$x->harga_beli, "retur_konsumen");
        }
    }
    function retur_agen($id_produk, $jumlah, $harga, $agen)
    {
        $x = $this->produk->get_produk_by_id($id_produk);
        #produk,tanggal,jumlah,harga,agen,refund
        $tanggal = date("Y-m-d H:i:s");
        $refund = ($harga - $x->harga_beli) * $jumlah;
        $data = array(
            "tanggal" => $tanggal,
            "agen" => $agen,
            "produk" => $id_produk,
            "jumlah" => $jumlah,
            "harga" => $harga,
            "refund" => $refund
        );
        $q = $this->db->insert('transaksi_retur',$data);
        if($q){
            return $this->produk->tambah_stok_produk($id_produk,$jumlah,$x->harga_beli, "retur_konsumen");
        }
    }

}