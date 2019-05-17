™
lessS2260ÿParse error at line 16 column 5:

 6: #shorthands {
 7:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
 8:   background: url("img.jpg") center / 100px;
 9:   background: #fff url(image.png) center / 1px 100px repeat-x scroll content-box padding-box;
10: }
11: #misc {
12:   background-image: url(images/image.jpg);
13: }
14: #data-uri {
15:   background: url(data:image/png;charset=utf-8;base64,
16:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
17:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
18:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
19:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
20:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
21:   background-image: url("http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700");
22: }
23: 
24: #svg-data-uri {
25:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
26: } 2 A