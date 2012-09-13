<?php
$html = <<<HTML
<ul class="baseinfo">
		<li class="link"><a charset="411-2-11" href="http://v.youku.com/v_show/id_XMTA1OTY2Mzc2.html" target="_blank" title="靠山"></a></li>
<li class="thumb"><img src="http://res.mfs.ykimg.com/050E000050077C29979273016201E510" alt="靠山"></li>
			<li class="status"><span class="bg"></span></li>
		<li class="ishd"><span class="ico__HD"></span></li>
		
		<li class="row1 rate">
	<span class="ratingstar"><label>评分:</label><span class="rating" title="有4,506人顶过
有971人踩过"><em class="ico__ratefull"></em><em class="ico__ratefull"></em><em class="ico__ratefull"></em><em class="ico__ratenull"></em><em class="ico__ratenull"></em><em class="num">6.2</em></span></span>

<span class="rating_dp"><label>豆瓣:</label>暂无</span>
</li>	
			
		<li class="row2">
		<span class="pub"><label>发行:</label>2007-01-01</span>
		</li>
	
			
		<li class="row2">
			<span class="area">
<label>地区:</label>
	<a charset="411-2-1" href="http://www.youku.com/v_olist/c_97_a_大陆.html" target="_blank">大陆</a></span>	
			<span class="type">
	<label>类型:</label>
						<a target="_blank" charset="411-2-8" href="http://www.youku.com/v_olist/c_97_g_军事.html">军事</a> / 					<a target="_blank" charset="411-2-8" href="http://www.youku.com/v_olist/c_97_g_历史.html">历史</a>			</span>	
		</li>
		<li class="row1">
			<span class="actor">
	<label>主演:</label>
						<a href="http://www.youku.com/star_page/uid_UNjc4MDQ=.html" charset="411-2-10" target="_blank">高曙光</a> / 					<a href="http://www.youku.com/star_page/uid_UNjY0MDg=.html" charset="411-2-10" target="_blank">蒋恺</a> / 					<a href="http://www.youku.com/star_page/uid_UNjYwNzY=.html" charset="411-2-10" target="_blank">李琳</a> / 					<a href="http://www.youku.com/star_page/uid_UMzEzODM3Mg==.html" charset="411-2-10" target="_blank">杨璐</a> / 					<a href="http://www.youku.com/star_page/uid_UMzIxOTk1Mg==.html" charset="411-2-10" target="_blank">杜若溪</a>			</span>	
		</li>
	</ul>
HTML;
$ary = array();
preg_match('/<ul class="baseinfo">\s*<li class="link">.*<\/a><\/li>\s*<li class="thumb">\s*<img.*src=[\'"]{1}(.*)[\'"]{1}[^>]*><\/li>.*<li class="row1 rate">.*<\/li>(.*)<\/ul>/siU', $html, $ary);
echo "<xmp>";var_dump($ary);echo "</xmp>";
?>