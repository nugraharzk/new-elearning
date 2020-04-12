<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Orangtua extends MY_Controller
{
  function __construct()
  {
    parent::__construct();

    must_login();
  }

  function index($segment_3 = '', $segment_4 = '')
  {
    if (!is_admin()) {
      redirect('welcome');
    }

    $status_id = $segment_3;
    if ($status_id == '' or $status_id > 2) {
      $status_id = 1;
    }

    $page_no = (int) $segment_4;
    if (empty($page_no)) {
      $page_no = 1;
    }

    $base_url_module = 'orangtua/index/' . $status_id . '/';

    # ambil semua data pengajar
    $retrieve_all = $this->orangtua_model->retrieve_all(20, $page_no, $status_id);
    // output_json($retrieve_all);
    $data['status_id']  = $status_id;
    $data['orangtua']   = $retrieve_all['results'];
    $data['pagination'] = $this->pager->view($retrieve_all, $base_url_module);
    $data['count_pending'] = $this->orangtua_model->count('pending');

    # panggil colorbox
    $html_js = load_comp_js(array(
      base_url('assets/comp/colorbox/jquery.colorbox-min.js'),
    ));
    $data['comp_js']  = $html_js;
    $data['comp_css'] = load_comp_css(array(base_url('assets/comp/colorbox/colorbox.css')));

    if (isset($_POST['status_id']) and !empty($_POST['status_id'])) {
      $post_status_id = $this->input->post('status_id', TRUE);
      $pengajar_ids   = $this->input->post('orangtua_id', TRUE);

      foreach ($pengajar_ids as $pengajar_id) {
        $retrieve_pengajar = $this->orangtua_model->retrieve($pengajar_id);
        if (!empty($retrieve_pengajar)) {
          # update pengajar
          $this->orangtua_model->update(
            $retrieve_pengajar['id'],
            $retrieve_pengajar['nip'],
            $retrieve_pengajar['nama'],
            $retrieve_pengajar['jenis_kelamin'],
            $retrieve_pengajar['tempat_lahir'],
            $retrieve_pengajar['tgl_lahir'],
            $retrieve_pengajar['alamat'],
            $retrieve_pengajar['foto'],
            $post_status_id
          );

          if ($retrieve_pengajar['status_id'] == 0 && $post_status_id == 1) {
            @kirim_email_approve_pengajar($pengajar_id);
          }
        }
      }

      redirect($base_url_module);
    }

    $this->twig->display('list-orangtua.html', $data);
  }
}
