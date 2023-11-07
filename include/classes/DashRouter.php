<?php


namespace Dash;


class DashRouter
{
    public function get($request)
    {
        $request = preg_replace('/[^a-zA-Z0-9/]/', '', $request);

        switch ($request) {
            case 'login':
                $template = "user_login.twig";

                $values = array(
                    'page' => array(
                        'title' => "User Log-In Page",
                        'description' => "Please enter your username and password to login to the website",
                        'class' => "login",
                        'pic' => 'site-img.png'
                    )
                );
            break;
            case label2:

            break;
            case label3:
            break;
            default:
        }

    }
}