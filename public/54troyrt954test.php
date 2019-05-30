<?php
$combs = ['g','y','b','r','o','p',
		  'gy','gb','gr','go','gp',
		  'yb','yr','yo','yp',
		  'br','bo','bp',
		  'ro','rp',
		  'op',
		  'gyb','gyr','gyo','gyp','gbr','gbo','gbp','gro','grp','gop',
		  'ybr','ybo','ybp','yro','yrp','yop',
		  'bro','brp','bop',
		  'rop',
		  'gybr','gybo','gybp',
		  'ybro','ybrp',
		  'brop',
		  'gybro','gybrp',
		  'ybrop',
		  'gybrop'];
foreach($combs as $i){
	$xx = 15;
	$yy = 15;
	$gd = imagecreatetruecolor($xx, $yy);
	$black = imagecolorallocate($gd, 0, 0, 0);
	$g = imagecolorallocate($gd, 0, 204, 0);
	$y =  imagecolorallocate($gd, 255, 255, 0);
	$b =  imagecolorallocate($gd, 0, 0, 255);
	$r = imagecolorallocate($gd, 255, 0, 0);
	$o = imagecolorallocate($gd, 255, 128, 0);
	$p = imagecolorallocate($gd, 102, 0, 102);
	if(strlen($i)==2){
		foreach(range(0,7) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[0]);
			}
		}
		foreach(range(8,$yy) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[1]);
			}
		}
	}else if(strlen($i)==3){
		foreach(range(0,5) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[0]);
			}
		}
		foreach(range(6,10) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[1]);
			}
		}
		foreach(range(11,$yy) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[2]);
			}
		}
	}else if(strlen($i)==4){
		foreach(range(0,3) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[0]);
			}
		}
		foreach(range(4,7) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[1]);
			}
		}
		foreach(range(8,11) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[2]);
			}
		}
		foreach(range(12,$yy) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[3]);
			}
		}
	}else if(strlen($i)==5){
		foreach(range(0,2) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[0]);
			}
		}
		foreach(range(3,5) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[1]);
			}
		}
		foreach(range(6,8) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[2]);
			}
		}
		foreach(range(9,12) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[3]);
			}
		}
		foreach(range(13,$yy) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[4]);
			}
		}
	}else if(strlen($i)==6){
		foreach(range(0,2) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[0]);
			}
		}
		foreach(range(3,5) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[1]);
			}
		}
		foreach(range(6,7) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[2]);
			}
		}
		foreach(range(8,9) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[3]);
			}
		}
		foreach(range(10,12) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[4]);
			}
		}
		foreach(range(13,$yy) as $h){
			foreach(range(0,$xx) as $k){
				imagesetpixel($gd, $k,$h, $$i[5]);
			}
		}
	}else{
		foreach(range(0, $yy) as $_y){
			foreach(range(0, $xx) as $_x){
				imagesetpixel($gd, $_x,$_y, $$i);
			}
		}
	}
	
	imagesetpixel($gd, 7,3, $black);
	for($j = 4, $counter = 0; $j <= 7; $j++, $counter++){
		for($h = 6-$counter; $h <= 8+$counter; $h++){
			imagesetpixel($gd, $h,$j, $black);
		}
	}
	foreach(range(4,10) as $j){
		imagesetpixel($gd, $j, 8, $black);
	}
	foreach(range(9, 11) as $h){
		foreach(range(4,10) as $j){
			if($j != 7)
				imagesetpixel($gd, $j,$h, $black);
		}
	}
	imagepng($gd, $i.'.png');
	imagedestroy($gd);
}
?>