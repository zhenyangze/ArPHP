<?php
/**
 * Ar for PHP .
 *
 * @author ycassnr<ycassnr@gmail.com>
 */

/**
 * class ArController.
 */
class ArController {

    protected $_assign = array();

    public function init()
    {

    }

    public function assign(array $vals)
    {
        foreach ($vals as $key => $val) :
            $this->_assign[$key] = $val;
        endforeach;

    }

    public function display($view = '')
    {
        $viewPath = arCfg('PATH.VIEW');

        $r = Ar::a('ArWebApplication')->route;

        if (empty($view)) :
            $viewPath .= $r['c'] . DS . $r['a'];
        elseif(strpos($view, '/') !== false) :
            $viewPath .= str_replace('/', DS, $view);
        else :
            $viewPath .= $r['c'] . DS . $view;
        endif;

        $viewFile = $viewPath . '.php';

        if (is_file($viewFile)) :
            extract($this->_assign);
            include $viewFile;
        else :
            throw new ArException('view : ' . $viewFile . ' not found');
        endif;

        exit;

    }

    public function redirect($r = array(), $show = '', $time = '0')
    {
        $route = empty($r[0]) ? '' : $r[0];

        $param = empty($r[1]) ? array() : $r[1];

        $url = Ar::createUrl($route, $param);

        $redirectUrl =
<<<str
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Refresh" content="$time;URL=$url" />
</head>
<body>
$show<a href="$url">立即跳转</a>
</body>
</html>
str;
        echo $redirectUrl;
        exit;

    }

    public function redirectSuccess($r = array(), $show = '', $time = '1')
    {
        $this->redirect($r, '成功:' . $show, $time);

    }

    public function redirectError($r = array(), $show = '' , $time = '2')
    {
        $this->redirect($r, '失败:' . $show, $time);

    }

    public function showJson($data, array $options = array())
    {
        if (empty($options['showJson']) && arComp('validator.validator')->checkAjax()) :
            header('charset:utf-8');
            header('Content-type:text/plain');
            if (empty($options['data'])) :
                $retArr = array(
                        'ret_code' => '1000',
                        'ret_msg' => '',
                    );

                if (is_array($data)) :
                    if (empty($data['ret_code']) && empty($data['ret_msg'])) :

                        $retArr['data'] = $data;
                        $retArr['total_lines'] = Ar::c('validator.validator')->checkMutiArray($data) ? (string)count($data) : 1;

                        $retArr = array_merge($retArr, $options);
                    else :
                        if (!empty($data['error_msg']))
                            $retArr['ret_code'] = "1001";
                        $retArr = array_merge($retArr, $data);
                    endif;
                else :
                    $retArr['ret_msg'] = $data;
                endif;
            else :
                $retArr = $data;
            endif;

            echo json_encode($retArr);
        else :
            return $data;
        endif;

    }

    public function ifLogin()
    {
        return !!arComp('list.session')->get('uid');

    }

    public function logOut()
    {
        arComp('list.session')->set('uid', null);

    }

    public function auth()
    {

    }

}