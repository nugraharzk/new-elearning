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

  function add($segment_3 = '')
  {
      # harus login sebagai admin
      if (!is_admin()) {
        redirect('welcome');
      }

      $status_id = $segment_3;
      if ($status_id == '' OR $status_id > 3) {
        redirect('orangtua/index/1');
      }

      $data['status_id'] = $status_id;

      $config['upload_path']   = get_path_image();
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['max_size']      = '0';
      $config['max_width']     = '0';
      $config['max_height']    = '0';
      $config['file_name']     = 'orangtua-'.url_title($this->input->post('nama', TRUE), '-', true);
      $this->upload->initialize($config);

      if (!empty($_FILES['userfile']['tmp_name']) AND !$this->upload->do_upload()) {
          $data['error_upload'] = '<span class="text-error">'.$this->upload->display_errors().'</span>';
          $error_upload = true;
      } else {
          $data['error_upload'] = '';
          $error_upload = false;
      }

      if ($this->form_validation->run('orangtua/add') == TRUE AND !$error_upload) {
          $nama          = $this->input->post('nama', TRUE);
          $siswa_id      = $this->input->post('siswa_id', TRUE);
          $jenis_kelamin = $this->input->post('jenis_kelamin', TRUE);
          $alamat        = $this->input->post('alamat', TRUE);
          $username      = $this->input->post('username', TRUE);
          $password      = $this->input->post('password2', TRUE);
          
          if (!empty($_FILES['userfile']['tmp_name'])) {
            $upload_data = $this->upload->data();

            # create thumb small
            $this->create_img_thumb(
                get_path_image($upload_data['file_name']),
                '_small',
                '50',
                '50'
            );

            # create thumb medium
            $this->create_img_thumb(
                get_path_image($upload_data['file_name']),
                '_medium',
                '150',
                '150'
            );

            $foto = $upload_data['file_name'];
        } else {
            $foto = null;
        }

          # simpan data orang tua
          $orangtua_id = $this->orangtua_model->create(
              $nama,
              $siswa_id,
              $jenis_kelamin,
              $alamat,
              $foto,
              1
          );

          # simpan data login
          $this->login_model->create(
              $username,
              $password,
              null,
              null,
              $orangtua_id,
              $is_admin,
              1
          );

          $this->session->set_flashdata('orangtua', get_alert('success', 'Data Pengajar berhasil disimpan.'));
          redirect('orangtua/index/1');

      } else {
        $upload_data = $this->upload->data();
        if (!empty($upload_data) AND is_file(get_path_image($upload_data['file_name']))) {
            unlink(get_path_image($upload_data['file_name']));
        }
      }

      $siswa = $this->db->get('siswa')->result();
      $orangtua = $this->db->get('orangtua')->result();
      
      $available = [];
      $found = false;
      foreach ($siswa as $row) {
        $found = false;
        foreach ($orangtua as $col) {
          if ($row->id == $col->siswa_id) {
            $found = true;
          }
        }
        if ($found == false) {
          array_push($available, $row);
        }
      }
      $data['siswa_available'] = $available;

      // output_json($available);
      $this->twig->display('tambah-orangtua.html', $data);
  }
}
