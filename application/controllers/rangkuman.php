<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class untuk resource Materi
 *
 * @package   e-Learning Dokumenary Net
 * @author    Almazari <almazary@gmail.com>
 * @copyright Copyright (c) 2013 - 2016, Dokumenary Net.
 * @since     1.0
 * @link      http://dokumenary.net
 *
 * INDEMNITY
 * You agree to indemnify and hold harmless the authors of the Software and
 * any contributors for any direct, indirect, incidental, or consequential
 * third-party claims, actions or suits, as well as any related expenses,
 * liabilities, damages, settlements or fees arising from your use or misuse
 * of the Software, or a violation of any terms of this license.
 *
 * DISCLAIMER OF WARRANTY
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESSED OR
 * IMPLIED, INCLUDING, BUT NOT LIMITED TO, WARRANTIES OF QUALITY, PERFORMANCE,
 * NON-INFRINGEMENT, MERCHANTABILITY, OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * LIMITATIONS OF LIABILITY
 * YOU ASSUME ALL RISK ASSOCIATED WITH THE INSTALLATION AND USE OF THE SOFTWARE.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS OF THE SOFTWARE BE LIABLE
 * FOR CLAIMS, DAMAGES OR OTHER LIABILITY ARISING FROM, OUT OF, OR IN CONNECTION
 * WITH THE SOFTWARE. LICENSE HOLDERS ARE SOLELY RESPONSIBLE FOR DETERMINING THE
 * APPROPRIATENESS OF USE AND ASSUME ALL RISKS ASSOCIATED WITH ITS USE, INCLUDING
 * BUT NOT LIMITED TO THE RISKS OF PROGRAM ERRORS, DAMAGE TO EQUIPMENT, LOSS OF
 * DATA OR SOFTWARE PROGRAMS, OR UNAVAILABILITY OR INTERRUPTION OF OPERATIONS.
 */

class Rangkuman extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        must_login();
    }

    private function formatData($val)
    {
        # cari pembuatnya
        if (!empty($val['pengajar_id'])) {
            $pengajar = $this->pengajar_model->retrieve($val['pengajar_id']);
            $val['pembuat'] = $pengajar;
            if (is_admin()) {
                $val['pembuat']['link_profil'] = site_url('pengajar/detail/'.$pengajar['status_id'].'/'.$pengajar['id']);
            } else {
                $val['pembuat']['link_profil'] = site_url('pengajar/detail/'.$pengajar['id']);
            }
        }
        if (!empty($val['orangtua_id'])) {
            $siswa = $this->orangtua_model->retrieve($val['orangtua_id']);
            $val['pembuat'] = $siswa;
            if (is_admin()) {
                $val['pembuat']['link_profil'] = site_url('orangtua/detail/'.$siswa['status_id'].'/'.$siswa['id']);
            } else {
                $val['pembuat']['link_profil'] = site_url('orangtua/detail/'.$siswa['id']);
            }
        }

        # cari materi kelas
        $materi_kelas = $this->materi_model->retrieve_all_kelas($val['id']);
        foreach ($materi_kelas as $mk) {
            $kelas = $this->kelas_model->retrieve($mk['kelas_id']);
            $val['materi_kelas'][] = $kelas;
        }

        # cari matapelajarannya
        $val['mapel'] = $this->mapel_model->retrieve($val['mapel_id']);

        # hitung jumlah komentar
        $val['jml_komentar'] = $this->komentar_model->count_by('materi', $val['id']);

        return $val;
    }

    function index($segment_3 = '')
    {
        if (!empty($_GET['clear_filter']) AND $_GET['clear_filter'] == 'true') {
            $this->session->set_userdata('filter_materi', null);
        }

        $page_no = (int)$segment_3;
        if (empty($page_no)) {
            $page_no = 1;
        }

        # jika ada post filter
        if ($this->form_validation->run('materi/filter') == true) {
            $pembuat = $this->input->post('pembuat', TRUE);

            # cari id pengajar dan siswa
            $pengajar_id = array();
            $siswa_id    = array();
            if (!empty($pembuat)) {
                foreach ($this->pengajar_model->retrieve_all_by_name($pembuat) as $val) {
                    $pengajar_id[] = $val['id'];
                }

                foreach ($this->siswa_model->retrieve_all_by_name($pembuat) as $val) {
                    $siswa_id[] = $val['id'];
                }
            }

            $filter = array(
                'judul'       => $this->input->post('judul', true),
                'konten'      => $this->input->post('konten', true),
                'pengajar_id' => $pengajar_id,
                'siswa_id'    => $siswa_id,
                'pembuat'     => $pembuat,
                'mapel_id'    => $this->input->post('mapel_id', true),
                'kelas_id'    => $this->input->post('kelas_id', true),
                'type'        => $this->input->post('type', true),
                'publish'     => is_siswa() ? array(1) : $this->input->post('publish', true),
            );

            $this->session->set_userdata('filter_materi', $filter);
        }

        $filter = $this->session->userdata('filter_materi');
        if (empty($filter)) {
            $filter = array(
                'judul'       => '',
                'konten'      => '',
                'pengajar_id' => array(),
                'siswa_id'    => array(),
                'pembuat'     => '',
                'mapel_id'    => array(),
                'kelas_id'    => array(),
                'type'        => array(),
                'publish'     => is_siswa() ? array(1) : array(),
            );
        }
        $data['filter'] = $filter;

        if (is_orangtua()) {
            $kelas_aktif = $this->siswa_kelas_aktif;
            $retrieve_kelas = $this->kelas_model->retrieve($kelas_aktif['kelas_id']);

            // # ambil parentnya
            $filter['kelas_id'] = array($retrieve_kelas['parent_id']);
            // $filter['siswa_id'] = get_sess_data('user', 'id');
        }

        # jika sebagai pengajar tampilkan materi miliknya saja
        if (is_pengajar()) {
            $filter['pengajar_id'] = get_sess_data('user', 'id');
        }

        # kalo siswa tampilkan yang kelasnya saja
        if (is_siswa()) {
            show_404();
        }

        # ambil semua data materi
        $retrieve_all_materi = $this->materi_model->retrieve_all(
            20,
            $page_no,
            $filter['pengajar_id'],
            $filter['siswa_id'],
            $filter['mapel_id'],
            $filter['judul'],
            $filter['konten'],
            $tgl_posting = null,
            $filter['publish'],
            $filter['kelas_id'],
            $filter['type']
        );

        # format array data
        $results = array();
        foreach ($retrieve_all_materi['results'] as $key => $val) {
            $results[$key] = $this->formatData($val);
        }

        $data['materi']      = $results;
        $data['pagination']  = $this->pager->view($retrieve_all_materi, 'materi/index/');
        $data['kelas']       = $this->kelas_model->retrieve_all(null, array('aktif' => 1));
        $data['mapel']       = $this->mapel_model->retrieve_all_mapel();

        // $this->tampilkan($_SESSION['login_' . APP_PREFIX]);
        // $this->tampilkan($results);
        // output_json($data);
        $this->twig->display('list-rangkuman.html', $data);
    }

    function detail($segment_3 = '')
    {
        $materi_id = (int)$segment_3;

        if (empty($materi_id)) {
            show_404();
        }

        $materi = $this->materi_model->retrieve($materi_id);
        if (empty($materi)) {
            show_404();
        }

        $data['materi'] = $materi;

        /**
         * Jika siswa cek dengan kelas aktif
         */
        if (is_siswa()) {
            show_404();
        }

        
        # setup texteditor
        $html_js = get_texteditor();

        # setup colorbox
        $html_js .= load_comp_js(array(
            base_url('assets/comp/colorbox/jquery.colorbox-min.js'),
        ));

        $data['comp_js']  = $html_js;
        $data['comp_css'] = load_comp_css(array(
            base_url('assets/comp/colorbox/colorbox.css')
        ));
        
        $data['siswas'] = $this->db->select('a.*, b.nama, b.nis, d.nama as nama_kelas')
                                   ->where('a.materi_id', $data['materi']['id'])
                                   ->group_by('a.siswa_id')
                                   ->join('siswa b', 'b.id=a.siswa_id')
                                   ->join('kelas_siswa c', 'c.id=a.siswa_id')
                                   ->join('kelas d', 'd.id=c.id')
                                   ->get('log_belajar a')
                                   ->result();
                                   
        if (is_orangtua()) {
            $data['materi']['siswa_id'] = get_sess_data('user','siswa_id');
            $data['is_orangtua'] = true;
            $data['teks'] = $this->db->select_sum('durasi')->where('materi_id', $data['materi']['id'])->where('tipe', 'teks')->where('siswa_id', $data['materi']['siswa_id'])->get('log_belajar')->row();
            $data['video'] = $this->db->select_sum('durasi')->where('materi_id', $data['materi']['id'])->where('tipe', 'video')->where('siswa_id', $data['materi']['siswa_id'])->get('log_belajar')->row();
            $data['audio'] = $this->db->select_sum('durasi')->where('materi_id', $data['materi']['id'])->where('tipe', 'audio')->where('siswa_id', $data['materi']['siswa_id'])->get('log_belajar')->row();
        }
        if (is_pengajar()) {
            $data['materi']['siswa_id'] = get_sess_data('user','id');
            $data['is_orangtua'] = false;
            $data['teks'] = $this->db->select_sum('durasi')->where('materi_id', $data['materi']['id'])->where('tipe', 'teks')->get('log_belajar')->row();
            $data['video'] = $this->db->select_sum('durasi')->where('materi_id', $data['materi']['id'])->where('tipe', 'video')->get('log_belajar')->row();
            $data['audio'] = $this->db->select_sum('durasi')->where('materi_id', $data['materi']['id'])->where('tipe', 'audio')->get('log_belajar')->row();
        }

        $data['teks'] = $data['teks']->durasi !== null ? $data['teks'] : 0;
        $data['video'] = $data['video']->durasi !== null ? $data['video'] : 0;
        $data['audio'] = $data['audio']->durasi !== null ? $data['audio'] : 0;

        $data['jquery'] = base_url("/assets/comp/jquery/jquery.min.js");
        $data['chartjs'] = base_url("/assets/comp/chartjs/Chart.min.js");
        $data['base_url'] = base_url("/");

        // $this->tampilkan($data);
        // $this->tampilkan($_SESSION['login_' . APP_PREFIX]);
        $this->twig->display('list-detail-rangkuman.html', $data);
    }

    public function getrow($id, $materiId)
    {
        $data['teks'] = $this->db->select_sum('durasi')
                         ->where('siswa_id', $id)
                         ->where('materi_id', $materiId)
                         ->where('tipe', 'teks')
                         ->get('log_belajar')
                         ->row();
        $data['video'] = $this->db->select_sum('durasi')
                         ->where('siswa_id', $id)
                         ->where('materi_id', $materiId)
                         ->where('tipe', 'video')
                         ->get('log_belajar')
                         ->row();
        $data['audio'] = $this->db->select_sum('durasi')
                         ->where('siswa_id', $id)
                         ->where('materi_id', $materiId)
                         ->where('tipe', 'audio')
                         ->get('log_belajar')
                         ->row();
        $data['teks'] = $data['teks']->durasi !== null ? $data['teks'] : 0;
        $data['video'] = $data['video']->durasi !== null ? $data['video'] : 0;
        $data['audio'] = $data['audio']->durasi !== null ? $data['audio'] : 0;
        $this->tampilkan($data);
    }

    public function tampilkan($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
