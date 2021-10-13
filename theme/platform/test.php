<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php'); 
	print_r($member);
?>


<link rel="stylesheet" href="<?=G5_THEME_URL?>/_common/css/level_structure.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
<script>
		var depthMap = {
			0 : 'lvl-one',
			1 : 'lvl-two',
			2 : 'lvl-three',
			3 : 'lvl-four',
			4 : 'lvl-five',
			5 : 'lvl-six',
			6 : 'lvl-seven',
			7 : 'lvl-eight',
			8 : 'lvl-nine',
			9 : 'lvl-ten'
		};

		function depthFirstTreeSort(arr, cmp) {
			// Returns an object, where each key is a node number, and its value
			// is an array of child nodes.
			function makeTree(arr) {
				var tree = {};
				for (var i = 0; i < arr.length; i++) {
					if (!tree[arr[i].mb_recommend_no]) tree[arr[i].mb_recommend_no] = [];
					tree[arr[i].mb_recommend_no].push(arr[i]);
				}
				return tree;
			}

			// For each node in the tree, starting at the given id and proceeding
			// depth-first (pre-order), sort the child nodes based on cmp, and
			// call the callback with each child node.
			function depthFirstTraversal(tree, id, cmp, callback) {

				var children = tree[id];
				//console.log(children);
				if (children) {
					children.sort(cmp);
					for (var i = 0; i < children.length; i++) {
						//console.log(children)
						callback(children[i]);
						depthFirstTraversal(tree, children[i].mb_no, cmp, callback);
					}
				}
			}

			// Overwrite arr with the reordered result
			var i = 0;
			var tree = makeTree(arr);
			// console.log(tree);
			// var minkey = _.minBy(_.keys(tree), function(o) { return Number(o); });
			// console.log(minkey);
	
			depthFirstTraversal(tree, arr[0].mb_recommend_no, cmp, function(node) {
				arr[i++] = node;
			});
		}
		
		// function nameCmp(a, b) { return a.mb_id.localeCompare(b.mb_id); }
		function nameCmp(a, b) { return a.mb_no < b.mb_no; }
		
		var $selected;
		$(function() {
			// 상세보기 
      /*xx
			$(document).on('click','.lvl' ,function(e) {
				$(this).toggleClass('lvl-is-open');
				$selected = $(this).next();
				if($selected.css('max-height') != '0px' ){
					$selected.css('max-height','0px');
				}else{
					$selected.css('max-height', $selected.prop('scrollHeight') + 'px');
				}
				// console.log($(this).attr('mb_no'));
				if($(this).hasClass('lvl-is-open')){
					$.get( "level_structure_upgraded.mem.php", {
						mb_no: $(this).attr('mb_no')
					}).done(function( data ) {
						if(data){
							$selected.find('.name').text(data.mb_id);
							$selected.find('.sponsor').text(data.mb_recommend);
							$selected.find('.enroll').text(data.enrolled);
							if(data.mb_level > 1 && data.mb_level < 9){
								$selected.find('.rank').text((data.mb_level -2) + ' Star');
							}
							$selected.find('.email').text(data.mb_email);
							$selected.find('.pool1').text(data.it_pool1);
							$selected.find('.pool2').text(data.it_pool2);
							$selected.find('.pool3').text(data.it_pool3);
							$selected.find('.gpu').text(data.it_gpu);
						}
					}).fail(function(e) {
						console.log( e );
					});
				}
			});

*/ /* 클릭 상세보기 제거 0618_moon */ 


			 $(document).on('click','.lvl-username' ,function(e) {
				console.log($(this).text());
			 	//getLeg('<?=$member['mb_id']?>', $(this).text());
				//getList( $(this).text(),name);
			 	//e.stopPropagation();

				getList($(this).text(), 'name');
				getLeg('<?=$member['mb_id']?>', $(this).text());
				$('.search_container').removeClass("active");

			 });

			$(document).on('click','.lvl > img' ,function(e) {
				var con = $(this).parents('.lvl-container');
				// con.attr('class').replace('lvl-container ','')
				var level = con.attr('class').replace('lvl-container ','');
				// console.log(level);
				if(con.hasClass('closed')){
					con.nextUntil( "." + level ).removeClass('closed').show();
					con.removeClass('closed');
				}else{
					con.nextUntil( "." + level ).hide();
					con.addClass('closed');
				}
				e.stopPropagation();
			});




			$(document).on('click','.go' ,function(e) {
				getList($(this).attr('mb_no'));
				getLeg('<?=$member['mb_id']?>', $(this).attr('mb_id'));
				e.stopPropagation();
			});

			$(document).on('click','.mbId' ,function(e) {
				getList($(this).text(), 'name');
				getLeg('<?=$member['mb_id']?>', $(this).text());
				$('.search_container').removeClass("active");
			});

			$('button.search-button').click(function(){
				if($("#binary_seach").val() == ""){
					//alert("Please enter a keyword.");
					commonModal('Notice','Please enter a keyword.',80);
					$("#binary_seach").focus();
					return;
				}
				
				$.get( "/bbs/level_structure_upgraded.search.php", {
					keyword: $("#binary_seach").val()
				}).done(function( data ) {
					$('.search_container').addClass("active");
					var vHtml = $('<div>');
					$.each(data, function( index, member ) {
						var line = $('<div>').append($('<strong>').addClass('mbId').html(member.mb_id));
						if(member.mb_name != ''){
							line.append('<br>');
							line.append( '(' + member.mb_name + ')');
						}else{
							line.css('line-height','50px');
						}
						vHtml.append(line);
					});
					$("#search_result").html(vHtml.html());
				}).fail(function(e) {
					console.log( e );
				});
			});

			$(".search_container .result_btn").on('click',function(e){
				//console.log(1);
				$('.search_container').removeClass("active");
			});
			
			$('#binary_seach').on('keydown',function(e){
				if(e.which == 13) {
					e.preventDefault();
					$('button.search-button').trigger('click');
				}
			});

			// 조직도 데이터 가져오기
			getList(Number('<?=$member['mb_no']?>'));

		});

		var xhr;
		// 검색하는 부분
		function getMember(mb_id){
			$("#now_id").val(mb_id);

			getList($(this).attr('mb_no'));
			getLeg('<?=$member['mb_id']?>', mb_id);
		}

		function getList(member_no, type){
			$.get( "/bbs/level_structure_upgraded.list.php", {
				mb_no: member_no,
				type : type
			}).done(function( data ) {
				//tt = data;
				var minObj = _.minBy(data, function(o) { return Number(o.depth); });
				
				console.log(data);

				_.forEach(data, function(member) {
					member.treelvl = member.depth - minObj.depth
				});
				// ascending sort
				depthFirstTreeSort(data, nameCmp);
				$('#total').text(data.length);

				var vHtml = $('<div>');
				$.each(data, function( index, member ) {
					var row = $('#dup .lvl-container').clone();
					row.addClass(depthMap[member.treelvl]);
					row.find('.lvl-username').text(member.mb_id);
					row.find('.depth').text(member.treelvl + 1);

					// row.find('.ranked').text(member.is_rise);

					if(member.is_rise == '1'){
						row.find('.ranked').addClass("fa-long-arrow-alt-up").addClass("green").text('('+member.rday+')');
					}else if(member.is_rise == '0'){
						row.find('.ranked').addClass("fa-long-arrow-alt-down").addClass("red").text('('+member.rday+')');
					}else{
						row.find('.ranked').text('-');
					}

					row.find('.sale30').text("$ "+ numeral(member.thirty).format('0,0'));
					row.find('.saleAll').text("$ "+ numeral(member.noo).format('0,0'));
					row.find('.childCnt').text(member.cnt);
					row.find('.go').attr('mb_no',member.mb_no);
					row.find('.go').attr('mb_id',member.mb_id);


					/*
					if(member.mb_level < 9){
						row.find('.lvl img').prop('src','images/' + member.mb_level + 'eos.png');
					}
					*/

					row.find('.lvl').attr('mb_no',member.mb_no);
					vHtml.append(row);
				});
				
				$('#levelStructure').html(vHtml.html());
				$("html, body").animate({ scrollTop: 0 }, "fast");
				//console.log(data);
			}).fail(function(e) {
				console.log( e );
			})
		}

		// 찾는 아이디에서 조상까지의 경로를 표시
		function getLeg(lastParent, findId){
			$.get("level_structure.leg.php", {
				lastParent : lastParent,
				findId : findId
			}).done(function( data ) {
				var reversed = data.reverse(); 
				//console.log(reversed);
				var vHtml = $('<div>');
				$.each(reversed, function( index, str ) {
					if(vHtml.html() == ''){
						vHtml.append($('<span>').addClass('mbId').text(str));
					}else{
						vHtml.append(" -> ").append($('<span>').addClass('mbId').text(str));
					}
				});
				$('.leg-view-container .gray').html(vHtml.html());
			}).fail(function(e) {
				console.log( e );
			});
		}

	</script>






		<section class="v_center structure_wrap">
			<p>데이터 크기로 인해 한번에 5대씩 화면에 나타납니다</p>
			<div class="btn_input_wrap">
				<input type="text" placeholder="회원찾기" />
				<a href="">검색</a>
			</div>
			<div class="bin_top">추천 계보</div>
			
			<div class="main-container">
			<div id="body-wrapper" class="big-container-wrapper">
				<div id="levelStructure" ></div>
			</div>
		</div>
	 

		<div style="display:none;" id="dup">
			<div class="lvl-container" >
				<div class="lvl">
					<!--<img src="" alt="lvl_img"> -->
					<span class="lvl-username"></span> 
				</div>
				</div>			
			</div>
	  </div>

			
			
			<div class="accordion_wrap">
			<!--
				<dl>
					<dt>
						<p class="s_v5">V5</p>
						<strong>Dream123</strong>
						<div>
							<b>추천</b> : 4 &#47;
							<b>단계</b> : 0 &#47;
							<b>입금</b> : &#36;100,000 &#47;
							<b>산하매출</b> : &#36;20,000,000
						</div>
					</dt>
					<dd>
						<div>
							<p>
								<span>이름 : </span>
								<strong>Kang il</strong>
							</p>
							<p>
								<span>매출일 : </span>
								<strong>2018-05-09</strong>
							</p>
							<p>
								<span>추천인 : </span>
								<strong>moll123456</strong>
							</p>
						</div>
						<div>
							<p>
								<span>직급 : </span>
								<strong>V5</strong>
							</p>
							<p>
								<span>상태 : </span>
								<strong>Active</strong>
							</p>
							<p>
								<span>이메일 : </span>
								<strong>qwer123@gmail.com</strong>
							</p>
						</div>
						<a href="#">go</a>
					</dd>
				</dl>
				-->
				<!--
				<dl class="dl_2depth">
					<dt>
						<p class="s_v3">V3</p>
						<strong>Rose</strong>
						<div>
							<b>추천</b> : 4 &#47;
							<b>단계</b> : 1 &#47;
							<b>입금</b> : &#36;100,000 &#47;
							<b>산하매출</b> : &#36;20,000,000
						</div>
					</dt>
					<dd>
						<div>
							<p>
								<span>이름 : </span>
								<strong>Kang il</strong>
							</p>
							<p>
								<span>매출일 : </span>
								<strong>2018-05-09</strong>
							</p>
							<p>
								<span>추천인 : </span>
								<strong>moll123456</strong>
							</p>
						</div>
						<div>
							<p>
								<span>직급 : </span>
								<strong>V3</strong>
							</p>
							<p>
								<span>상태 : </span>
								<strong>Active</strong>
							</p>
							<p>
								<span>이메일 : </span>
								<strong>qwer123@gmail.com</strong>
							</p>
						</div>
						<a href="#">go</a>
					</dd>
				</dl>
				<dl class="dl_3depth">
					<dt>
						<p class="s_v3">V3</p>
						<strong>Hong</strong>
						<div>
							<b>추천</b> : 4 &#47;
							<b>단계</b> : 2 &#47;
							<b>입금</b> : &#36;100,000 &#47;
							<b>산하매출</b> : &#36;20,000,000
						</div>
					</dt>
					<dd>
						<div>
							<p>
								<span>이름 : </span>
								<strong>Kang il</strong>
							</p>
							<p>
								<span>매출일 : </span>
								<strong>2018-05-09</strong>
							</p>
							<p>
								<span>추천인 : </span>
								<strong>moll123456</strong>
							</p>
						</div>
						<div>
							<p>
								<span>직급 : </span>
								<strong>V3</strong>
							</p>
							<p>
								<span>상태 : </span>
								<strong>Active</strong>
							</p>
							<p>
								<span>이메일 : </span>
								<strong>qwer123@gmail.com</strong>
							</p>
						</div>
						<a href="#">go</a>
					</dd>
				</dl>
				<dl class="dl_4depth">
					<dt>
						<p>V0</p>
						<strong>img</strong>
						<div>
							<b>추천</b> : 4 &#47;
							<b>단계</b> : 3 &#47;
							<b>입금</b> : &#36;100,000 &#47;
							<b>산하매출</b> : &#36;20,000,000
						</div>
					</dt>
					<dd>
						<div>
							<p>
								<span>이름 : </span>
								<strong>Kang il</strong>
							</p>
							<p>
								<span>매출일 : </span>
								<strong>2018-05-09</strong>
							</p>
							<p>
								<span>추천인 : </span>
								<strong>moll123456</strong>
							</p>
						</div>
						<div>
							<p>
								<span>직급 : </span>
								<strong>V0</strong>
							</p>
							<p>
								<span>상태 : </span>
								<strong>Active</strong>
							</p>
							<p>
								<span>이메일 : </span>
								<strong>qwer123@gmail.com</strong>
							</p>
						</div>
						<a href="#">go</a>
					</dd>
				</dl>
				<dl class="dl_5depth">
					<dt>
						<p class="s_v1">V1</p>
						<strong>sing</strong>
						<div>
							<b>추천</b> : 4 &#47;
							<b>단계</b> : 4 &#47;
							<b>입금</b> : &#36;100,000 &#47;
							<b>산하매출</b> : &#36;20,000,000
						</div>
					</dt>
					<dd>
						<div>
							<p>
								<span>이름 : </span>
								<strong>Kang il</strong>
							</p>
							<p>
								<span>매출일 : </span>
								<strong>2018-05-09</strong>
							</p>
							<p>
								<span>추천인 : </span>
								<strong>moll123456</strong>
							</p>
						</div>
						<div>
							<p>
								<span>직급 : </span>
								<strong>V1</strong>
							</p>
							<p>
								<span>상태 : </span>
								<strong>Active</strong>
							</p>
							<p>
								<span>이메일 : </span>
								<strong>qwer123@gmail.com</strong>
							</p>
						</div>
						<a href="#">go</a>
					</dd>
				</dl>
				<dl class="dl_5depth">
					<dt>
						<p class="s_v2">V2</p>
						<strong>number</strong>
						<div>
							<b>추천</b> : 4 &#47;
							<b>단계</b> : 4 &#47;
							<b>입금</b> : &#36;100,000 &#47;
							<b>산하매출</b> : &#36;20,000,000
						</div>
					</dt>
					<dd>
						<div>
							<p>
								<span>이름 : </span>
								<strong>Kang il</strong>
							</p>
							<p>
								<span>매출일 : </span>
								<strong>2018-05-09</strong>
							</p>
							<p>
								<span>추천인 : </span>
								<strong>moll123456</strong>
							</p>
						</div>
						<div>
							<p>
								<span>직급 : </span>
								<strong>V2</strong>
							</p>
							<p>
								<span>상태 : </span>
								<strong>Active</strong>
							</p>
							<p>
								<span>이메일 : </span>
								<strong>qwer123@gmail.com</strong>
							</p>
						</div>
						<a href="#">go</a>
					</dd>
				</dl>
				<dl class="dl_4depth">
					<dt>
						<p class="s_v4">V4</p>
						<strong>img</strong>
						<div>
							<b>추천</b> : 4 &#47;
							<b>단계</b> : 3 &#47;
							<b>입금</b> : &#36;100,000 &#47;
							<b>산하매출</b> : &#36;20,000,000
						</div>
					</dt>
					<dd>
						<div>
							<p>
								<span>이름 : </span>
								<strong>Kang il</strong>
							</p>
							<p>
								<span>매출일 : </span>
								<strong>2018-05-09</strong>
							</p>
							<p>
								<span>추천인 : </span>
								<strong>moll123456</strong>
							</p>
						</div>
						<div>
							<p>
								<span>직급 : </span>
								<strong>V4</strong>
							</p>
							<p>
								<span>상태 : </span>
								<strong>Active</strong>
							</p>
							<p>
								<span>이메일 : </span>
								<strong>qwer123@gmail.com</strong>
							</p>
						</div>
						<a href="#">go</a>
					</dd>
				</dl>
				<dl class="dl_4depth">
					<dt>
						<p class="s_v7">V7</p>
						<strong>hyde</strong>
						<div>
							<b>추천</b> : 4 &#47;
							<b>단계</b> : 3 &#47;
							<b>입금</b> : &#36;100,000 &#47;
							<b>산하매출</b> : &#36;20,000,000
						</div>
					</dt>
					<dd>
						<div>
							<p>
								<span>이름 : </span>
								<strong>Kang il</strong>
							</p>
							<p>
								<span>매출일 : </span>
								<strong>2018-05-09</strong>
							</p>
							<p>
								<span>추천인 : </span>
								<strong>moll123456</strong>
							</p>
						</div>
						<div>
							<p>
								<span>직급 : </span>
								<strong>V7</strong>
							</p>
							<p>
								<span>상태 : </span>
								<strong>Active</strong>
							</p>
							<p>
								<span>이메일 : </span>
								<strong>qwer123@gmail.com</strong>
							</p>
						</div>
						<a href="#">go</a>
					</dd>
				</dl>
				<dl class="dl_2depth">
					<dt>
						<p class="s_v6">V6</p>
						<strong>Rose</strong>
						<div>
							<b>추천</b> : 4 &#47;
							<b>단계</b> : 1 &#47;
							<b>입금</b> : &#36;100,000 &#47;
							<b>산하매출</b> : &#36;20,000,000
						</div>
					</dt>
					<dd>
						<div>
							<p>
								<span>이름 : </span>
								<strong>Kang il</strong>
							</p>
							<p>
								<span>매출일 : </span>
								<strong>2018-05-09</strong>
							</p>
							<p>
								<span>추천인 : </span>
								<strong>moll123456</strong>
							</p>
						</div>
						<div>
							<p>
								<span>직급 : </span>
								<strong>V3</strong>
							</p>
							<p>
								<span>상태 : </span>
								<strong class="font_red">Inactive</strong>
							</p>
							<p>
								<span>이메일 : </span>
								<strong>qwer123@gmail.com</strong>
							</p>
						</div>
						<a href="#">go</a>
					</dd>
				</dl>
					-->	
			</div>
			
		</section>

		<div class="gnb_dim"></div>

	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_structure.png' alt='아이콘'> 조직도 보기");
			

			$('.accordion_wrap dd').css("display", "none");
			$('.accordion_wrap dt').click(function() {
				$(this).next().stop().slideToggle();
			});
		});
	</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>

