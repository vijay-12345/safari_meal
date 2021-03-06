<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
          <title>@yield('title')</title>
          <style type="text/css">
              body {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                margin:0 !important;
                width: 100% !important;
                -webkit-text-size-adjust: 100% !important;
                -ms-text-size-adjust: 100% !important;
                -webkit-font-smoothing: antialiased !important;
              }
              .tableContent img {
                width:50%;
                border-radius:2px;
              }
              a {
                color:#382F2E;
              }
              p, h1 {
                color:#382F2E;
                margin:0;
              }
              p {
                text-align:left;
                color:#999999;
                font-size:14px;
                font-weight:normal;
                line-height:19px;
              }
              a.link1 {
                color:#382F2E;
              }
              a.link2 {
                font-size:16px;
                text-decoration:none;
                color:#ffffff;
              }
              h2 {
                text-align:left;
                color:#222222; 
                font-size:19px;
                font-weight:normal;
              }
              div,p,ul,h1 {
                margin:0;
              }
              .bgBody {
                background: #ffffff;
              }
              .bgItem {
                background: #ffffff;
              }
          </style>
          <script type="colorScheme" class="swatch active">
          {
              "name":"Default",
              "bgBody":"ffffff",
              "link":"382F2E",
              "color":"999999",
              "bgItem":"ffffff",
              "title":"222222"
          }
          </script>
        </head>

        <body paddingwidth="0" paddingheight="0"   style="padding-top: 0; padding-bottom: 0; padding-top: 0; padding-bottom: 0; background-repeat: repeat; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased;" offset="0" toppadding="0" leftpadding="0">
          
          <table border="0" cellspacing="0" cellpadding="0" class="tableContent bgBody" align="center" style="font-family: Verdana, Geneva, sans-serif; border:1px solid #e3e3e3;width:580px;text-align:center;">
            <thead style="background-color:#2dca03;">
              <tr>
                <td>
                  <a href="{{url('/')}}" title=""><img src="{{url('images/logo.png')}}" alt=""></a>
                </td>
              </tr>
            </thead>

            <tbody>
                <tr>
                  <td>
                    @yield('content')
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0 0 10px 30px; font-weight: bold;text-align:left">
                    {{ trans('email.regards') }}
                    <br>
                    <span style='color:#222222;'>Taxiye Food Team</span>
                  </td>
                </tr>
            </tbody>

            <tfoot style="background-color:#393737;color:#fff;">
              <tr>
                <td style="padding:15px;">
                  <ul class="list-inline" style="list-style:none;">
                    <li style="display:inline-block;"><a title='Facebook' href="https://www.facebook.com/Taxiye Food/" target="_blank" style="color:#fff;text-decoration:none;"><img src="{{ url('images/fb-btn.png') }}" alt='facebook'></a></li>
                    <li style="display:inline-block;"><a title='Twitter' href="https://twitter.com/Taxiye Food" target="_blank" style="color:#fff;text-decoration:none;"><img src="{{ url('images/twitter-btn.png') }}" alt='twitter'></a></li>
                    <li style="display:inline-block;"><a title='Instagram' href="https://www.instagram.com/Taxiye Food/" target="_blank" style="color:#fff;text-decoration:none;"><img src="{{ url('images/instagram-btn.png') }}" alt='Instagram'></a></li>
                  </ul>
                </td>
              </tr>
            </tfoot>
          </table>
          
        </body>
    </html>