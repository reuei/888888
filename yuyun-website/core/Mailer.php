<?php
/**
 * 语云科技企业官网 - 邮件发送类
 */

class Mailer {
    private $config;

    public function __construct() {
        $this->config = get_config();
    }

    /**
     * 发送邮件
     */
    public function send($to, $subject, $body, $isHtml = true) {
        // 尝试使用PHPMailer
        if ($this->usePHPMailer()) {
            return $this->sendWithPHPMailer($to, $subject, $body, $isHtml);
        }

        // 使用mail()函数
        return $this->sendWithMail($to, $subject, $body, $isHtml);
    }

    /**
     * 使用PHPMailer发送
     */
    private function sendWithPHPMailer($to, $subject, $body, $isHtml) {
        try {
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                require_once YUYUN_ROOT . '/core/PHPMailer/src/Exception.php';
                require_once YUYUN_ROOT . '/core/PHPMailer/src/PHPMailer.php';
                require_once YUYUN_ROOT . '/core/PHPMailer/src/SMTP.php';
            }

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            // SMTP配置
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'] ?? 'smtp.qq.com';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_user'] ?? '';
            $mail->Password = $this->config['smtp_pass'] ?? '';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->config['smtp_port'] ?? 465;

            // 发件人
            $mail->setFrom(
                $this->config['smtp_from'] ?? $this->config['smtp_user'] ?? 'noreply@yuyun.com',
                $this->config['site_name'] ?? '语云科技'
            );

            // 收件人
            $mail->addAddress($to);

            // 内容
            $mail->Subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $mail->Body = $body;
            $mail->isHTML($isHtml);

            // 字符集
            $mail->CharSet = 'UTF-8';

            $mail->send();
            return true;
        } catch (Exception $e) {
            log_message("PHPMailer发送失败: " . $mail->ErrorInfo, 'error');
            return false;
        }
    }

    /**
     * 使用mail()函数发送
     */
    private function sendWithMail($to, $subject, $body, $isHtml) {
        $headers = [];

        // MIME版本
        $headers[] = 'MIME-Version: 1.0';

        // Content-Type
        if ($isHtml) {
            $headers[] = 'Content-type: text/html; charset=UTF-8';
        } else {
            $headers[] = 'Content-type: text/plain; charset=UTF-8';
        }

        // From
        $fromName = $this->config['site_name'] ?? '语云科技';
        $fromEmail = $this->config['smtp_from'] ?? 'noreply@yuyun.com';
        $headers[] = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <$fromEmail>";

        // 回复地址
        $headers[] = 'Reply-To: ' . $fromEmail;

        // X-Mailer
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        // 编码主题
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        return mail($to, $encodedSubject, $body, implode("\r\n", $headers));
    }

    /**
     * 是否使用PHPMailer
     */
    private function usePHPMailer() {
        return !empty($this->config['smtp_host']) &&
               !empty($this->config['smtp_user']) &&
               !empty($this->config['smtp_pass']);
    }
}
