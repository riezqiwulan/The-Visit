<?php
defined('BASEPATH') or exit('No direct script acces allowed');

class ModelDestinasi extends CI_Model
{
    //manajemen destinasi
    public function getDestinasi()
    {
        return $this->db->get('destinasi');
    }

    public function destinasiWhere($where)
    {
        return $this->db->get_where('destinasi', $where);
    }

    public function simpanDestinasi($data = null)
    {
        $this->db->insert('destinasi', $data);
    }

    public function updateDestinasi($data = null, $where = null)
    {
        $this->db->update('destinasi', $data, $where);
    }

    public function hapusDestinasi($field, $where)
    {
        $this->db->delete('destinasi', $where);
    }

    public function total($field, $where)
    {
        $this->db->select_sum($field);
        if(! empty($where) && count($where) > 0){
            $this->db->where($where);
        }
        $this->db->from('destinasi');
        return $this->db->get()->row($field);
    }

    //manajemen kategori
    public function getKategori() 
    {
        return $this->db->get('kategori');
    }

    public function kategoriWhere($where)
    {
        return $this->db->get_where('kategori', $where);
    }

    public function simpanKategori($data = null)
    {
        $this->db->insert('kategori', $data);
    }

    public function hapusKategori($where = null)
    {
        $this->db->delete('kategori', $where);
    }

    public function updateKategori($where = null, $data = null)
    {
        $this->db->update('kategori', $data, $where);
    }

    //join
    public function joinKategoridestinasi($where)
    {
        $this->db->select('destinasi.id_kategori,kategori.kategori');
        $this->db->from('destinasi');
        $this->db->join('kategori', 'kategori.id = destinasi.id_kategori');
        $this->db->where($where);
        return $this->db->get();
    }
}