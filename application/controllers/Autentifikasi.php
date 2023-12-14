<?php 
class Autentifikasi extends CI_Controller 
{ 
    
    public function index() 
    { 
        //jika statusnya sudah login, maka tidak bisa mengakses halaman login alias dikembalikan ke tampilan user 
        if($this->session->userdata('email')){ 
            redirect('user'); 
            }
            $this->form_validation->set_rules('email', 'Alamat Email', 'required|trim|valid_email', [ 
                'required' => 'Email Harus diisi!!', 
                'valid_email' => 'Email Tidak Benar!!' 
            ]); 
            $this->form_validation->set_rules('password', 'Password', 'required|trim', [ 
                'required' => 'Password Harus diisi' 
                ]); 
                if ($this->form_validation->run() == false) { 
                    $data['judul'] = 'Login'; 
                    $data['user'] = ''; 
                    //kata 'login' merupakan nilai dari variabel judul dalam array $data dikirimkan ke view aute_header 
                    $this->load->view('templates/aute_header', $data); 
                    $this->load->view('autentifikasi/login'); 
                    $this->load->view('templates/aute_footer'); 
                    } else { 
                        $this->_login(); 
                    } 
                        
                }

                private function _login() 
                { 
                    $email = htmlspecialchars($this->input->post('email', true)); 
                    $password = $this->input->post('password', true); 
                    $user = $this->ModelUser->cekData(['email' => $email])->row_array(); 
                    
                    //jika usernya ada 
                    if ($user) { 
                        //jika user sudah aktif 
                        if ($user['is_active'] == 1) { 
                            //cek password 
                            if (password_verify($password, $user['password'])) { 
                                $data = [ 
                                    'email' => $user['email'], 
                                    'role_id' => $user['role_id'] ];
                                $this->session->set_userdata($data); 
                                
                                if ($user['role_id'] == 1) { 
                                    redirect('admin'); 
                                    } else { 
                                        if ($user['image'] == 'default.jpg') { 
                                            $this->session->set_flashdata('pesan', 
                                            '<div class="alert alert-info alert-message" role="alert">Silahkan 
                                            Ubah Profile Anda untuk Ubah Photo Profil</div>'); 
                                            } 
                                            redirect('user'); 
                                            } 
                                            } else { 
                                                $this->session->set_flashdata('pesan', 
                                                '<div class="alert alert-danger alert-message" role="alert">Password 
                                                salah!!</div>'); 
                                                redirect('autentifikasi'); 
                                                } 
                                             } else { 
                                                $this->session->set_flashdata('pesan', 
                                                '<div class="alert alert-danger alert-message" role="alert">User 
                                                belum diaktivasi!!</div>'); 
                                                redirect('autentifikasi'); 
                                                } 
                                                } else { 
                                                $this->session->set_flashdata('pesan', 
                                                '<div class="alert alert-danger alert-message" role="alert">Email 
                                                tidak terdaftar!!</div>'); 
                                                redirect('autentifikasi'); 
                                                } 
                }

                public function blok()
                {
                    $this->load->view('autentifikasi/blok');
                }        
                
                public function gagal()
                {
                    $this->load->view('autentifikasi/gagal');
                }

                public function registrasi()
                {
                    if ($this->session->userdata('email')) {
                        redirect('user');
                    }

                    $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required', [
                        'required' => 'Nama Belum diisi!'
                    ]);

                    $this->form_validation->set_rules('email', 'Alamat Email', 'required|trim|valid_email|is_unique[user.email]', [
                        'valid_email' => 'Email Tidak Benar!',
                        'required' => 'Email Belum diisi!',
                        'is_unique' => 'Email Sudah Terdaftar'
                    ]);

                    $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
                        'matches' => 'Password Tidak Sama!',
                        'min_length' => 'Password terlalu pendek'
                    ]);

                    $this->form_validation->set_rules('password2', 'Repeat Password', 'required|trim|matches[password1]');

                    if ($this->form_validation->run() == false) {
                        $data['judul'] = 'Registrasi Akun';
                        $this->load->view('templates/aute_header', $data);
                        $this->load->view('autentifikasi/registrasi');
                        $this->load->view('templates/aute_footer');
                    } else {
                        $email = $this->input->post('email', true);
                        $data = [ 
                            'nama' => htmlspecialchars($this->input->post('nama', true)),
                            'email' => htmlspecialchars($email),
                            'image' => 'default.jpg',
                            'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                            'role_id' => 2, 
                            'is_active' => 0, 
                            'tanggal_input' => time() 
                        ];

                        $token = base64_encode(random_bytes(32));
                        $user_token = [
                            'email' => $email,
                            'token' => $token, 
                            'date_created' => time()
                        ];

                        $this->ModelUser->simpanData($data);
                        $this->ModelUser->simpanToken($user_token);
                        $this->_sendEmail($token, 'verify');

                        $this->session->set_flashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Selamat!! Akun Anda Sudah Dibuat. Silakan Aktivasi Akun Anda</div>');
                        redirect('autentifikasi');
                    }
                }
                
                private function _sendEmail($token, $type)
                {
                    $config = [
                        'protocol' => 'smtp',
                        'smtp_host' => 'ssl://smtp.googlemail.com',
                        'smtp_user' => '12220852@bsi.ac.id',
                        'smtp_pass' => 'Sunday0twelfth',
                        'smtp_port' => 465,
                        'mailtype' => 'html',
                        'charset' => 'utf-8',
                        'newline' => "\r\n",
                    ];

                    $this->load->library('email', $config);
                    $this->email->from('12220852@bsi.ac.id', 'Pustaka Booking');
                    $this->email->to($this->input->post('email'));

                    if ($type == 'verify')
                    {
                        $this->email->subject('Verifikasi Akun');
                        $this->email->message('Klik link berikut untuk verifikasi akun anda : <a href="'. base_url() . 'autentifikasi/verify?email='
                        . $this->input->post('email') . '&token=' . urlencode($token) . '">Aktivasi Di sini</a>');
                    } else if($type == 'forgot') {
                        $this->email->subject('Reset Password');
                        $this->email->message('Klik link berikut untuk reset password anda : <a href="'. base_url() . 'autentifikasi/
                        resetpassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Reset Password</a>');
                    }

                    if ($this->email->send()) {
                        return true;
                    } else {
                        echo $this->email->print_debugger();
                        die;
                    }
                }

                public function verify()
                {
                    $email = $this->input->get('email');
                    $token = $this->input->get('token');
                    $user = $this->db->get_where('user', ['email' => $email])->row_array();

                    if ($user) {
                        $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
                        if ($user_token) {
                            if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                                $this->db->set('is_active', 1);
                                $this->db->where('email', $email);
                                $this->db->update('user');
                                $this->db->delete('user_token', ['email' => $email]);
                                $this->session->set_flashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Email '. $email . 'Sudah diaktivasi. Silakan Login</div>');
                                redirect('autentifikasi');
                            } else {
                                $this->db->delete('user', ['email' => $email]);
                                $this->db->delete('user_token', ['email' => $email]);
                                $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-messsage" role="aler">Aktivasi Akun Gagal!! Token Expired!!</div>');
                                redirect('autentifikasi');
                            }
                        } else {
                                $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Aktivasi Akun Gagal!! Token Salah!!</div>');
                                redirect('autentifikasi');
                            }
                    } else {
                        $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Aktivasi Akun Gagal!! Email Salah!!</div>');
                        redirect('autentifikasi');
                    }
                }

                public function logout()
                {
                    $this->session->unset_userdata('email');
                    $this->session->unset_userdata('role_id');
                    $this->session->set_flashdata('pesan', '<div class=" alert alert-success alert-message" role="alert">Anda Berhasil Logout</div>');
                    redirect('autentifikasi');
                }

                public function lupaPassword()
                {
                    $this->form_validation->set_rules('email', 'Alamat Email', 'required|trim|valid_email', [
                        'required' => 'Email Harus Diisi!!',
                        'valid_email' => 'Email Tidak Benar!!'
                    ]);
                    if ($this->form_validation->run() == false) {
                        $data['judul'] = 'Lupa Password';
                        $this->load->view('templates/aute_header', $data);
                        $this->load->view('autentifikasi/lupa-password');
                        $this->load->view('templates/aute_footer');
                    } else {
                        $email = $this->input->post('email');
                        $user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();
                        if ($user) {
                            $token = base64_encode(random_bytes(32));
                            $user_token = [
                                'email' => $email,
                                'token' => $token,
                                'date_created' => $time()
                            ];
                            $this->ModelUser->simpanToken($user_token);
                            $this->_sendEmail($token, 'forgot');
                            $this->session->set_flashdata('pesan', '<div class="alert alert-succes alert-message" role="alert">Silakan Cek Email Anda Untuk Reset Password</div>');
                            redirect('autentifikasi/lupaPassword');
                        } else {
                            $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message role="alert">Email Tidak Terdaftar atau Belum Diaktivasi!!</div>');
                            redirect('autentifikasi/lupaPassword');
                        }
                    }
                }

                public function resetPassword()
                {
                    $email = $this->input->get('email');
                    $token = $this->input->get('token');
                    $user = $this->db->get_where('user', ['email' => $email])->row_array();

                    if ($user) {
                        $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
                        
                        if ($user_token) {
                            $this->session->set_userdata('reset_email',$email);
                            $this->ubahPassword();
                        } else {
                            $this->session->set_flashdata('pesan', '<div class="aler alert-danger alert-message" role="alert">Reset Password Gagal!! Token Salah!!</div>');
                            redirect('autentifikasi/lupaPassword');
                        }
                    } else {
                        $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Reset Password Gagal!! Email Salah!!</div>');
                        redirect('autentifikasi/lupaPassword');
                    }
                }

                public function ubahPassword()
                {
                    if(!$this->session->userdata('reset_email')) {
                        redirect('autentifikasi');
                    }
                    $this->form_validation->set_rules('password_baru1', 'Password Baru', 'required|trim|min_length[3]|matches[password_baru2]', [
                        'matches' => 'Password Tidak Sama!!',
                        'min_length' => 'Password Terlalu Pendek!!',
                        'required' => 'Password Belum Diisi!!'
                    ]);
                    $this->form_validation->set_rules('password_baru2', 'Ulangi Password Baru', 'required|trim|matches[password_baru1]', [
                        'matches' => 'Ulangi Password Tidak Sama!!',
                        'required' => 'Ulangi Password Belum Diisi!!'
                    ]);

                    if ($this->form_validation->run() == false) {
                        $data['judul'] = 'Ubah Password';
                        $this->load->view('templates/aute_header', $data);
                        $this->load->view('autentifikasi/new-password');
                        $this->load->view('templates/aute_footer');
                    } else {
                        $password = password_hash($this->input->post('password_baru1'), PASSWORD_DEFAULT);
                        $email = $this->session->userdata('reset_email');

                        $this->db->set('password', $password);
                        $this->db->where('email', $email);
                        $this->db->update('user');

                        $this->session->unset_userdata('reset_email');

                        $this->db->delete('user_token', ['email' => $email]);

                        $this->session->set_flashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Password Berhasil Diubah! Silakan Login</div>');
                        redirect('autentifikasi');
                    }
                }
    }   