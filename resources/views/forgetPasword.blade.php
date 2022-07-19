
<!DOCTYPE html>
<html>
​
<head>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
            font-size: 15px;
            line-height: 1.5;
        }
    </style>
</head>
​
<body>
​
    <div style="background: #e1e4e6; padding:0 8px; width: 510px; margin: 0 auto; color: #363636; border-radius: 6px;">
        <table  style="padding: 6px 0;" align="center">
            <tr>
                <td style="text-align: center;">
                <img src="{{ asset('images/seasialogo.png') }}" alt="logo" />
                    
                </td>
            </tr>
        </table >
        <table width="500" align="center" style="background: #fff; padding:16px; border-radius: 6px; ">
            
            <tr>
                <td style="text-align: center;">
                    <h2 style="margin: 8px 0;">Hello {{$name}}</h2>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <p style="margin: 8px 0;"> Click the button below to <b>{{$title}}</b>.</p>
                  
                   
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                <a
                        style="margin: 2px 0; background-color: #AB0E0E; color: #fff; border-color: #AB0E0E; padding: 4px 8px; border-radius: 6px; font-size: 14px;"
                        href="{{$url}}" target="_blank">Click Here!</a>
                </td>
            </tr>
            <tr>
                <td>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td valign="top" width="33.333%" style="text-align: center; padding-right: 10px;">
                    <p>&copy; 2022 Seasia. All Rights Reserved</p>
                </td>
            </tr>
        </table>
    </date_interval_format>
</body>
​
</html>
