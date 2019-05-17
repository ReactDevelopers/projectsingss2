ª
cssS2260‘Parse error at line 22 column 5:

12: #shorthands {
13:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
14:   background: url("img.jpg") center / 100px;
15:   background: #fff url(image.png) center / 1px 100px repeat-x scroll content-box padding-box;
16: }
17: #misc {
18:   background-image: url(images/image.jpg);
19: }
20: #data-uri {
21:   background: url(data:image/png;charset=utf-8;base64,
22:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
23:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
24:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
25:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
26:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
27:   background-image: url("http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700");
28: }
29: #svg-data-uri {
30:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
31: }
32: .comma-delimited { 2 A