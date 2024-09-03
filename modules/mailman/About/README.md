Mailer for Trongate
====================

This module is for sending email, specifically 
the Simple Members Module.
=========================================

Question regarding this please post at trongate.io/help_bar
Put phpMailer in the title.
=========================================

Important!

If the following is set to true.
$mail->SMTPDebug = true; //SMTP::DEBUG_SERVER
After the mail is sent their is no redirect.
Set this to false after you veryify your mail is working.
==========================================
These 2 lines of code are to call in the function to send the email.

    $this->module('mailman');
    $this->mailman->_send_my_email($data)


Below is the code from the members module controller file.
Add the code after //NB!! to existing code
===Code is same for _send_activate_account_email also.===

    function _send_password_reset_email($member_obj, $reset_url) {
        //send an email inviting the user to goto the $reset url
        $data['subject'] = 'Password Reset';
        $data['target_name'] = $member_obj->first_name.' '.$member_obj->last_name;
        $data['member_obj'] = $member_obj;
        $data['reset_url'] = $reset_url;
        $data['target_email'] = $member_obj->email_address;
        $data['msg_html'] = $this->view('msg_password_reset_invite', $data, true);
        $msg_plain = str_replace('</p>', '\\n\\n', $data['msg_html']);
        $data['msg_plain'] = strip_tags($msg_plain);

        // NB!! new code for sending email: see module 'mailer' 
        
        $this->module('mailer');
        $this->mailer->send_my_email($data);
    }

