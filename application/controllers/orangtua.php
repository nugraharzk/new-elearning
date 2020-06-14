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

  function edit_profile($segment_3 = '', $segment_4 = '')
    {
        # siswa tidak diijinkan
        if (is_siswa()) {
            exit('Akses ditolak');
        }

        $status_id         = (int)$segment_3;
        $pengajar_id       = (int)$segment_4;
        $retrieve_pengajar = $this->orangtua_model->retrieve($pengajar_id);
        if (empty($retrieve_pengajar)) {
            exit('Data Orangtua tidak ditemukan');
        }

        # jika sebagai pengajar, hanya profilnya dia yang bisa diupdate
        if (is_pengajar() AND get_sess_data('user', 'id') != $retrieve_pengajar['id']) {
            exit('Akses ditolak');
        }

        $retrieve_login = $this->login_model->retrieve(null, null, null, null, null, $retrieve_pengajar['id']);
        $retrieve_pengajar['is_admin'] = $retrieve_login['is_admin'];

        $data['status_id']    = $status_id;
        $data['pengajar_id']  = $pengajar_id;
        $data['pengajar']     = $retrieve_pengajar;

        if ($this->form_validation->run('pengajar/edit_profile') == TRUE AND (!is_demo_app() OR !$retrieve_login['is_admin'])) {
            $nip           = $this->input->post('nip', TRUE);
            $nama          = $this->input->post('nama', TRUE);
            $jenis_kelamin = $this->input->post('jenis_kelamin', TRUE);
            $tempat_lahir  = $this->input->post('tempat_lahir', TRUE);
            $tgl_lahir     = $this->input->post('tgl_lahir', TRUE);
            $bln_lahir     = $this->input->post('bln_lahir', TRUE);
            $thn_lahir     = $this->input->post('thn_lahir', TRUE);
            $alamat        = $this->input->post('alamat', TRUE);
            if (is_admin()) {
                $status   = $this->input->post('status_id', TRUE);
                $is_admin = $this->input->post('is_admin', TRUE);
            } else {
                $status   = $retrieve_pengajar['status_id'];
                $is_admin = $retrieve_login['is_admin'];
            }

            if (empty($thn_lahir)) {
                $tanggal_lahir = null;
            } else {
                $tanggal_lahir = $thn_lahir.'-'.$bln_lahir.'-'.$tgl_lahir;
            }

            # update pengajar
            $this->pengajar_model->update(
                $pengajar_id,
                $nip,
                $nama,
                $jenis_kelamin,
                $tempat_lahir,
                $tanggal_lahir,
                $alamat,
                $retrieve_pengajar['foto'],
                $status
            );

            # update login
            $this->login_model->update(
                $retrieve_login['id'],
                $retrieve_login['username'],
                null,
                $pengajar_id,
                $is_admin,
                null
            );

            if ($retrieve_pengajar['status_id'] == 0 && $status == 1) {
                kirim_email_approve_pengajar($pengajar_id);
            }

            $this->session->set_flashdata('edit', get_alert('success', 'Profil pengajar berhasil diperbaharui.'));
            redirect('pengajar/edit_profile/'.$status_id.'/'.$pengajar_id);
        }

        $this->twig->display('edit-pengajar-profile.html', $data);
    }

    /**
     * Meghapus foto pengajar
     * @since 1.8
     */
    function delete_foto($segment_3 = "", $segment_4 = "")
    {
        # siswa tidak diijinkan
        if (is_siswa()) {
            show_error('Akses ditolak');
        }

        $pengajar_id       = (int)$segment_3;
        $retrieve_pengajar = $this->pengajar_model->retrieve($pengajar_id);
        if (empty($retrieve_pengajar)) {
            show_error('Data Pengajar tidak ditemukan');
        }

        # jika sebagai pengajar, hanya profilnya dia yang bisa diupdate
        if (is_pengajar() AND get_sess_data('user', 'id') != $retrieve_pengajar['id']) {
            show_error('Akses ditolak');
        }

        if (is_file(get_path_image($retrieve_pengajar['foto']))) {
            unlink(get_path_image($retrieve_pengajar['foto']));
        }

        if (is_file(get_path_image($retrieve_pengajar['foto'], 'medium'))) {
            unlink(get_path_image($retrieve_pengajar['foto'], 'medium'));
        }

        if (is_file(get_path_image($retrieve_pengajar['foto'], 'small'))) {
            unlink(get_path_image($retrieve_pengajar['foto'], 'small'));
        }

        $this->pengajar_model->delete_foto($retrieve_pengajar['id']);

        $uri_back = $segment_4;
        if (!empty($uri_back)) {
            $uri_back = deurl_redirect($uri_back);
        } else {
            $uri_back = site_url('pengajar');
        }

        redirect($uri_back);
    }

    function edit_picture($segment_3 = '', $segment_4 = '')
    {
        # siswa tidak diijinkan
        if (is_siswa()) {
            exit('Akses ditolak');
        }

        $status_id         = (int)$segment_3;
        $pengajar_id       = (int)$segment_4;
        $retrieve_pengajar = $this->pengajar_model->retrieve($pengajar_id);
        if (empty($retrieve_pengajar)) {
            exit('Data Pengajar tidak ditemukan');
        }

        # jika sebagai pengajar, hanya profilnya dia yang bisa diupdate
        if (is_pengajar() AND get_sess_data('user', 'id') != $retrieve_pengajar['id']) {
            exit('Akses ditolak');
        }

        $retrieve_login = $this->login_model->retrieve(null, null, null, null, $retrieve_pengajar['id']);
        $retrieve_pengajar['is_admin'] = $retrieve_login['is_admin'];

        $data['status_id']    = $status_id;
        $data['pengajar_id']  = $pengajar_id;
        $data['pengajar']     = $retrieve_pengajar;

        $config['upload_path']   = get_path_image();
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = '0';
        $config['max_width']     = '0';
        $config['max_height']    = '0';
        $config['file_name']     = 'pengajar-'.url_title($retrieve_pengajar['nama'], '-', true);
        $this->upload->initialize($config);

        if ($this->upload->do_upload() AND (!is_demo_app() OR !$retrieve_login['is_admin'])) {

            if (is_file(get_path_image($retrieve_pengajar['foto']))) {
                unlink(get_path_image($retrieve_pengajar['foto']));
            }

            if (is_file(get_path_image($retrieve_pengajar['foto'], 'medium'))) {
                unlink(get_path_image($retrieve_pengajar['foto'], 'medium'));
            }

            if (is_file(get_path_image($retrieve_pengajar['foto'], 'small'))) {
                unlink(get_path_image($retrieve_pengajar['foto'], 'small'));
            }

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

            # update pengajar
            $this->pengajar_model->update(
                $pengajar_id,
                $retrieve_pengajar['nip'],
                $retrieve_pengajar['nama'],
                $retrieve_pengajar['jenis_kelamin'],
                $retrieve_pengajar['tempat_lahir'],
                $retrieve_pengajar['tgl_lahir'],
                $retrieve_pengajar['alamat'],
                $upload_data['file_name'],
                $retrieve_pengajar['status_id']
            );

            $this->session->set_flashdata('edit', get_alert('success', 'Foto pengajar berhasil diperbaharui.'));
            redirect('pengajar/edit_picture/'.$status_id.'/'.$pengajar_id);
        } else {
            if (!empty($_FILES['userfile']['tmp_name'])) {
                $data['error_upload'] = '<span class="text-error">'.$this->upload->display_errors().'</span>';
            }
        }

        $this->twig->display('edit-pengajar-picture.html', $data);
    }

    function edit_username($segment_3 = '', $segment_4 = '')
    {
        # siswa tidak diijinkan
        if (is_siswa()) {
            exit('Akses ditolak');
        }

        $status_id         = (int)$segment_3;
        $pengajar_id       = (int)$segment_4;
        $retrieve_pengajar = $this->pengajar_model->retrieve($pengajar_id);
        if (empty($retrieve_pengajar)) {
            exit('Data Pengajar tidak ditemukan');
        }

        # jika sebagai pengajar, hanya profilnya dia yang bisa diupdate
        if (is_pengajar() AND get_sess_data('user', 'id') != $retrieve_pengajar['id']) {
            exit('Akses ditolak');
        }

        $data['status_id']    = $status_id;
        $data['pengajar_id']  = $pengajar_id;
        $data['login']        = $this->login_model->retrieve(null, null, null, null, $pengajar_id);

        if ($this->form_validation->run('pengajar/edit_username') == TRUE AND (!is_demo_app() OR !$data['login']['is_admin'])) {
            $login_id = $this->input->post('login_id', TRUE);
            $username = $this->input->post('username', TRUE);

            try {
                # update username
                $this->login_model->update(
                    $login_id,
                    $username,
                    null,
                    $pengajar_id,
                    $data['login']['is_admin'],
                    $data['login']['reset_kode']
                );
            } catch (Exception $e) {
                $this->session->set_flashdata('edit', get_alert('warning', $e->getMessage()));
                redirect('pengajar/edit_username/'.$status_id.'/'.$pengajar_id);
            }

            $this->session->set_flashdata('edit', get_alert('success', 'Username pengajar berhasil diperbaharui.'));
            redirect('pengajar/edit_username/'.$status_id.'/'.$pengajar_id);
        }

        $this->twig->display('edit-pengajar-username.html', $data);
    }

    function edit_password($segment_3 = '', $segment_4 = '')
    {
        # siswa tidak diijinkan
        if (is_siswa()) {
            exit('Akses ditolak');
        }

        $status_id         = (int)$segment_3;
        $pengajar_id       = (int)$segment_4;
        $retrieve_pengajar = $this->pengajar_model->retrieve($pengajar_id);
        if (empty($retrieve_pengajar)) {
            exit('Data Pengajar tidak ditemukan');
        }

        # jika sebagai pengajar, hanya profilnya dia yang bisa diupdate
        if (is_pengajar() AND get_sess_data('user', 'id') != $retrieve_pengajar['id']) {
            exit('Akses ditolak');
        }

        $data['status_id']    = $status_id;
        $data['pengajar_id']  = $pengajar_id;

        $retrieve_login = $this->login_model->retrieve(null, null, null, null, $pengajar_id);
        $data['login']  = $retrieve_login;

        if ($this->form_validation->run('pengajar/edit_password') == TRUE AND (!is_demo_app() OR !$retrieve_login['is_admin'])) {
            $password = $this->input->post('password2', TRUE);

            # update password
            $this->login_model->update_password($retrieve_login['id'], $password);

            $this->session->set_flashdata('edit', get_alert('success', 'Password pengajar berhasil diperbaharui.'));
            redirect('pengajar/edit_password/'.$status_id.'/'.$pengajar_id);
        }

        $this->twig->display('edit-pengajar-password.html', $data);
    }

    function detail($segment_3 = '', $segment_4 = '')
    {
        if (is_admin()) {
            $status_id         = (int)$segment_3;
            $orangtua_id       = (int)$segment_4;
            $data['status_id'] = $status_id;
        } else {
            $orangtua_id = (int)$segment_3;
        }

        $retrieve_orangtua = $this->orangtua_model->retrieve($orangtua_id);
        if (empty($retrieve_orangtua)) {
            redirect('orangtua');
        }

        $retrieve_login = $this->login_model->retrieve(null, null, null, null, null, $retrieve_orangtua['id']);

        $data['pengajar']       = $retrieve_orangtua;
        $nama = $this->db->select('nama')->where('id', $retrieve_orangtua['siswa_id'])->get('siswa')->row();
        $data['pengajar']['nama_siswa'] = $nama->nama;
        $data['pengajar_login'] = $retrieve_login;

        # panggil colorbox
        $html_js = load_comp_js(array(
            base_url('assets/comp/colorbox/jquery.colorbox-min.js'),
        ));
        $data['comp_js']  = $html_js;
        $data['comp_css'] = load_comp_css(array(base_url('assets/comp/colorbox/colorbox.css')));

        // output_json($data);
        $this->twig->display('detail-orangtua.html', $data);
    }

    public function delete_orangtua($id)
    {
      $this->db->where('id', $id)->delete('orangtua');
      $this->db->where('orangtua_id', $id)->delete('login');

      redirect('orangtua');
    }
}
