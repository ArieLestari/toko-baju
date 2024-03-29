<?php

class Warna extends Controller {

    function Warna()
    {
        parent::Controller();
        $this->load->model('Warna_model', 'warna');

        if(!$this->session->userdata('master_login'))
            redirect('master/home/login');

    }

    function index()
    {
        redirect('master/warna/manage');
    }

    function manage($sort_by = 'id')
    {
        $data = new stdClass();

        $data->id_warna_baru = 0;

        echo $this->input->get('search');

        // kalo ada bau-bau submit
        if($this->input->post('submit'))
        {
            $data->id_warna_baru = $this->insert();
        }

        if($this->input->post('edit_submit'))
        {
            $data->id_warna_baru = $this->update();
        }

        $data->daftar_warna = $this->warna->get_semua_warna($sort_by);
        $data->title = "Manajemen Warna";

        $data->sort_by = $sort_by;

        // view yang memuat isi halamannya
        $data->view_konten = "warna";

        // ambil view "master_base.php" (templet dasar)
        $this->load->view('master/master_base', $data);
    }

    function insert()
    {
        $warna = array(
            'nama' => $this->input->post('new_nama'),
            'keterangan' => $this->input->post('new_keterangan')
        );

        if (empty($warna['nama'])) redirect('master/warna');

        if ($this->warna->insert_warna($warna))
        {
            return $this->db->insert_id();
        }
    }

    function update()
    {
        $warna = array(
            'nama' => $this->input->post('nama'),
            'keterangan' => $this->input->post('keterangan')
        );

        $id_warna = intval($this->input->post('id'));

        if (empty($warna['nama'])) redirect('master/warna');

        if ($this->warna->update_warna($id_warna, $warna))
        {
            return $id_warna;
        }
    }

    function quick_entry()
    {
        // called via AJAX
        if ($this->input->post('new_nama'))
        {
            $id_warna_baru = $this->insert();

            echo $id_warna_baru;
            exit;
        }

        $this->load->view('master/warna_quick_entry');
    }

    function refresh_warna()
    {
        // me-refresh dropdown warna
        // via AJAX
        $daftar_warna = $this->warna->get_semua_warna();

        echo "<option>Pilih warna:</option>\n";
        foreach ($daftar_warna as $warna)
        {
            echo "<option value='{$warna['id']}'>{$warna['nama']}</option>\n";
        }
    }

    function hapus($id_warna)
    {
        // called via AJAX
        if ($this->warna->aman_dihapus($id_warna))
        {
            $q = $this->db->query("DELETE FROM warna WHERE id = {$id_warna}");

            if ($q) echo "1"; else echo "0";
            exit;

        } else echo "0";
    }
}