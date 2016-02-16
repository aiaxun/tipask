/**
 *  ueditor����������
 *  �������������������༭��������
 */
/**************************��ʾ********************************
 * ���б�ע�͵��������ΪUEditorĬ��ֵ��
 * �޸�Ĭ������������ȷ���Ѿ���ȫ��ȷ�ò�������ʵ��;��
 * ��Ҫ�������޸ķ�����һ����ȡ���˴�ע�ͣ�Ȼ���޸ĳɶ�Ӧ��������һ������ʵ�����༭��ʱ�����Ӧ������
 * �������༭��ʱ����ֱ��ʹ�þɰ������ļ��滻�°������ļ�,���õ��ľɰ������ļ�����ȱ���¹�������Ĳ��������½ű�����
 **************************��ʾ********************************/


(function () {
    /**
     * �༭����Դ�ļ���·����������ʾ�ĺ����ǣ��Ա༭��ʵ����ҳ��Ϊ��ǰ·����ָ��༭����Դ�ļ�����dialog���ļ��У���·����
     * ���ںܶ�ͬѧ��ʹ�ñ༭����ʱ����ֵ�����·�����⣬�˴�ǿ�ҽ�����ʹ��"�������վ��Ŀ¼�����·��"�������á�
     * "�������վ��Ŀ¼�����·��"Ҳ������б�ܿ�ͷ������"/myProject/ueditor/"������·����
     * ���վ�����ж������ͬһ�㼶��ҳ����Ҫʵ�����༭������������ͬһUEditor��ʱ�򣬴˴���URL���ܲ�������ÿ��ҳ��ı༭����
     * ��ˣ�UEditor�ṩ����Բ�ͬҳ��ı༭���ɵ������õĸ�·����������˵������Ҫʵ�����༭����ҳ�����д�����´��뼴�ɡ���Ȼ����Ҫ��˴���URL���ڶ�Ӧ�����á�
     * window.UEDITOR_HOME_URL = "/xxxx/xxxx/";
     */
    var URL = window.UEDITOR_HOME_URL || getUEBasePath();
    /**
     * ���������塣ע�⣬�˴������漰��·�������ñ���©URL������
     */
    window.UEDITOR_CONFIG = {

        //Ϊ�༭��ʵ�����һ��·����������ܱ�ע��
        UEDITOR_HOME_URL : URL

        //ͼƬ�ϴ�������
        ,imageUrl:g_site_url+"index.php?attach/uploadimage"             //ͼƬ�ϴ��ύ��ַ
        ,imagePath:g_site_url                     //ͼƬ������ַ��������fixedImagePath,�����������󣬿���������
        //,imageFieldName:"upfile"                  //ͼƬ���ݵ�key,���˴��޸ģ���Ҫ�ں�̨��Ӧ�ļ��޸Ķ�Ӧ����
        //,compressSide:0                           //�ȱ�ѹ���Ļ�׼��ȷ��maxImageSideLength�����Ĳ��ն���0Ϊ������ߣ�1Ϊ���տ�ȣ�2Ϊ���ո߶�
        //,maxImageSideLength:900                   //�ϴ�ͼƬ�������ı߳����������Զ��ȱ�����,�����ž�����һ���Ƚϴ��ֵ������������image.html��
        ,savePath: [ 'upload1', 'upload2', 'upload3' ]    //ͼƬ�����ڷ������˵�Ŀ¼�� Ĭ��Ϊ�գ� ��ʱ���ϴ�ͼƬʱ������������󱣴�ͼƬ��Ŀ¼�б�
                                                            // ����û���ϣ���������� �������������������������ܹ���Ӧ�ϵ�Ŀ¼�����б�
                                                            //���磺 savePath: [ 'upload1', 'upload2' ]

        //ͿѻͼƬ������
        ,scrawlUrl:URL+"php/scrawlUp.php"           //Ϳѻ�ϴ���ַ
        ,scrawlPath:URL+"php/"                            //ͼƬ������ַ��ͬimagePath

        //�����ϴ�������
        ,fileUrl:g_site_url+"index.php?attach/upload"               //�����ϴ��ύ��ַ
        ,filePath:g_site_url                  //����������ַ��ͬimagePath
        //,fileFieldName:"upfile"                    //�����ύ�ı��������˴��޸ģ���Ҫ�ں�̨��Ӧ�ļ��޸Ķ�Ӧ����

        //Զ��ץȡ������
        //,catchRemoteImageEnable:true               //�Ƿ���Զ��ͼƬץȡ,Ĭ�Ͽ���
        ,catcherUrl:URL +"php/getRemoteImage.php"   //����Զ��ͼƬץȡ�ĵ�ַ
        ,catcherPath:URL + "php/"                  //ͼƬ������ַ��ͬimagePath
        //,catchFieldName:"upfile"                   //�ύ����̨Զ��ͼƬuri�ϼ������˴��޸ģ���Ҫ�ں�̨��Ӧ�ļ��޸Ķ�Ӧ����
        //,separater:'ue_separate_ue'               //�ύ����̨��Զ��ͼƬ��ַ�ַ����ָ���
        //,localDomain:[]                            //���ض���������������Զ��ͼƬץȡʱ������֮����������������µ�ͼƬ������ץȡ������,Ĭ�ϲ�ץȡ127.0.0.1��localhost

        //ͼƬ���߹���������
        ,imageManagerUrl:URL + "php/imageManager.php"       //ͼƬ���߹���Ĵ����ַ
        ,imageManagerPath:URL + "php/"                                    //ͼƬ������ַ��ͬimagePath

        //��Ļ��ͼ������
        ,snapscreenHost: location.hostname                                 //��Ļ��ͼ��server���ļ����ڵ���վ��ַ����ip���벻Ҫ��http://
        ,snapscreenServerUrl: URL +"php/imageUp.php" //��Ļ��ͼ��server�˱������UEditor�ķ�������Ϊ��URL +"server/upload/php/snapImgUp.php"��
        ,snapscreenPath: URL + "php/"
        ,snapscreenServerPort: location.port                                   //��Ļ��ͼ��server�˶˿�
        //,snapscreenImgAlign: ''                                //��ͼ��ͼƬĬ�ϵ��Ű淽ʽ

        //wordת��������
        ,wordImageUrl:URL + "php/imageUp.php"             //wordת���ύ��ַ
        ,wordImagePath:URL + "php/"                       //
        //,wordImageFieldName:"upfile"                     //wordת��������˴��޸ģ���Ҫ�ں�̨��Ӧ�ļ��޸Ķ�Ӧ����

        //��Ƶ�ϴ�������
        ,getMovieUrl:URL+"php/getMovie.php"                   //��Ƶ���ݻ�ȡ��ַ
        ,videoUrl:URL+"php/fileUp.php"               //�����ϴ��ύ��ַ
        ,videoPath:URL + "php/"                   //����������ַ��ͬimagePath
        //,videoFieldName:"upfile"                    //�����ύ�ı��������˴��޸ģ���Ҫ�ں�̨��Ӧ�ļ��޸Ķ�Ӧ����

        //�������ϵ����еĹ��ܰ�ť�������򣬿�����new�༭����ʵ��ʱѡ���Լ���Ҫ�Ĵ��¶���

, toolbars: [["bold","forecolor","insertimage","attachment","unlink","link","insertvideo","map","insertcode","autotypeset","fullscreen"]]

        //����������Ŀ
        //,isShow : true    //Ĭ����ʾ�༭��

        //,initialContent:'��ӭʹ��ueditor!'    //��ʼ���༭��������,Ҳ����ͨ��textarea/script��ֵ������������

        //,initialFrameWidth:1000  //��ʼ���༭�����,Ĭ��1000
        //,initialFrameHeight:320  //��ʼ���༭���߶�,Ĭ��320
        //,zIndex : 900     //�༭���㼶�Ļ���,Ĭ����900

        //����Զ��壬��ø�p��ǩ���µ��иߣ�Ҫ����������ʱ������������
        //,initialStyle:'p{line-height:1em}'//�༭���㼶�Ļ���,���������ı������
        ,wordCount:false          //�Ƿ�������ͳ��
        ,elementPathEnabled : false
    };

    function getUEBasePath ( docUrl, confUrl ) {

        return getBasePath( docUrl || self.document.URL || self.location.href, confUrl || getConfigFilePath() );

    }

    function getConfigFilePath () {

        var configPath = document.getElementsByTagName('script');

        return configPath[ configPath.length -1 ].src;

    }

    function getBasePath ( docUrl, confUrl ) {

        var basePath = confUrl;


        if(/^(\/|\\\\)/.test(confUrl)){

            basePath = /^.+?\w(\/|\\\\)/.exec(docUrl)[0] + confUrl.replace(/^(\/|\\\\)/,'');

        }else if ( !/^[a-z]+:/i.test( confUrl ) ) {

            docUrl = docUrl.split( "#" )[0].split( "?" )[0].replace( /[^\\\/]+$/, '' );

            basePath = docUrl + "" + confUrl;

        }

        return optimizationPath( basePath );

    }

    function optimizationPath ( path ) {

        var protocol = /^[a-z]+:\/\//.exec( path )[ 0 ],
            tmp = null,
            res = [];

        path = path.replace( protocol, "" ).split( "?" )[0].split( "#" )[0];

        path = path.replace( /\\/g, '/').split( /\// );

        path[ path.length - 1 ] = "";

        while ( path.length ) {

            if ( ( tmp = path.shift() ) === ".." ) {
                res.pop();
            } else if ( tmp !== "." ) {
                res.push( tmp );
            }

        }

        return protocol + res.join( "/" );

    }

    window.UE = {
        getUEBasePath: getUEBasePath
    };

})();
