<?php
class Error{
    private $_mail = 'jjsogua@hotmail.com';
    
    public function __construct($detail = ''){
        $this->_send($detail);
    }
    
    private function _printr($array){
        ob_start();
        print_r($array);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    private function _send($detail){
        $backTrace = debug_backtrace();
        $subject = 'Error';
        $content = '<html>
        	<head></head>
        	<body>
        		<table border="1">        
					<tr>
						<td>Hora: </td>
						<td>' . date('d/m/Y H:i:s') . '</td>
					</tr>
        			<tr>
        				<td>Detalle: </td>
        				<td>' . $detail . '</td>
    				</tr>
        			<tr>
        				<td>Get: </td>
        				<td>
        					<pre>' . $this->_printr($_GET) . '</pre>
    					</td>
    				</tr>
        			<tr>
        				<td>Post: </td>
        				<td>
        					<pre>' . $this->_printr($_POST) . '</pre>
    					</td>
    				</tr>
        			<tr>
        				<td>Session: </td>
        				<td>
        					<pre>' . $this->_printr($_SESSION) . '</pre>
    					</td>
    				</tr>
        			<tr>
        				<td>Backtrace 0: </td>
        				<td>
        					<pre>' . $this->_printr($backTrace[0]) . '</pre>
    					</td>
    				</tr>
        			<tr>
        				<td>Backtrace 1: </td>
        				<td>
        					<pre>' . $this->_printr($backTrace[1]) . '</pre>
    					</td>
    				</tr>
        			<tr>
        				<td>Backtrace 2: </td>
        				<td>
        					<pre>' . $this->_printr($backTrace[2]) . '</pre>
    					</td>
    				</tr>
        			<tr>
        				<td>Backtrace 3: </td>
        				<td>
        					<pre>' . $this->_printr($backTrace[3]) . '</pre>
    					</td>
    				</tr>
    			</table>
			</body>
		</html>';

        @mail($this->_mail, $subject, $content, "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\nFrom: {$this->_mail}\r\nReturn-path: {$this->_mail}\r\n");
    }
}