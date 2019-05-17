Ð
lessS2260¶Parse error at line 18 column 5:

 8:   not-a-comment: url(//z);
 9: }
10: #shorthands {
11:   background: url("http://www.lesscss.org/spec.html") no-repeat 0 4px;
12: }
13: #misc {
14:   background-image: url(images/image.jpg);
15: }
16: #data-uri {
17:   background: url(data:image/png;charset=utf-8;base64,
18:     kiVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAABlBMVEUAAAD/
        ^
19:     k//+l2Z/dAAAAM0lEQVR4nGP4/5/h/1+G/58ZDrAz3D/McH8yw83NDDeNGe4U
20:     kg9C9zwz3gVLMDA/A6P9/AFGGFyjOXZtQAAAAAElFTkSuQmCC);
21:   background-image: url(data:image/x-png,f9difSSFIIGFIFJD1f982FSDKAA9==);
22:   background-image: url(http://fonts.googleapis.com/css?family=\"Rokkitt\":\(400\),700);
23: }
24: 
25: #svg-data-uri {
26:   background: transparent url('data:image/svg+xml, <svg version="1.1"><g></g></svg>');
27: }
28: 2 A