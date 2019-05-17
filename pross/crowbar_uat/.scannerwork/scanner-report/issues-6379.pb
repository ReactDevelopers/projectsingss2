™
lessS2260ÿParse error at line 19 column 5:

 9: #shorthands {
10:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
11:   background: url("img.jpg") center / 100px;
12:   background: #fff url(image.png) center / 1px 100px repeat-x scroll content-box padding-box;
13: }
14: #misc {
15:   background-image: url(images/image.jpg);
16: }
17: #data-uri {
18:   background: url(data:image/png;charset=utf-8;base64,
19:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
20:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
21:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
22:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
23:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
24:   background-image: url("http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700");
25: }
26: 
27: #svg-data-uri {
28:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
29: } 2 A