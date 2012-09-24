<?php
  class Constants{
  	const SUCC='00000';
	const USER_NOT_EXIST='20001';
	const ERROR_PASSWORD_INVALID='20002';
	const METHOD_NOT_SUPPORT="10021";
	const APP_KEY_INVALID='10006';
	const SYSTEM_ERROR='10001';
	const EMAIL_INVALID='20003';
	const USERNAME_IS_NULL='20004';
	const PWD_IS_NULL='20005';
	const USERNAME_EXIST='20006';
	const EMAIL_EXIST='20007';
	const SEESION_IS_EXPIRED='20008';
	const THIRD_PART_SOURCE_TYPE_INVALID='20009';
	const OBJECT_NOT_FOUND='20010';
	const PARAM_IS_INVALID='20011';
	const RESULT_IS_NULL='20012';
	const PROGRAM_IS_PUBLISHED='20013';
	const URL_INVALID='20014';
	const PROGRAM_IS_FAVORITY='20015';	
	const PROGRAM_IS_UN_FAVORITY='20016';
	const PERSON_IS_LIKED='20017';
	
	
	
	
	
	
	const THIRD_PART_ACCOUNT_SINA='1'; //sina
	const THIRD_PART_ACCOUNT_QQ='2';//QQ
	const THIRD_PART_ACCOUNT_REN_REN='3';//REN REN
	const THIRD_PART_ACCOUNT_DOUBAN='4';// dou ban
	const THIRD_PART_ACCOUNT_LOCAL_CONTACT='5';// ����ͨ��¼
	
	const USER_APPROVAL=1;
	const USER_DELETE=-1;
	
	const DYNAMIC_CONTENT_TYPE_MOVIE=1; //��Ӱ 
	const DYNAMIC_CONTENT_TYPE_TV=2;   //���� 
	const DYNAMIC_CONTENT_TYPE_SHOW=3;  //���ս�Ŀ 
	const DYNAMIC_CONTENT_TYPE_COMMENTS=4; //���� 
	
	const DYNAMIC_TYPE_WATCH=1;//���� ��Ŀ
	const DYNAMIC_TYPE_FAVORITY=2; //�ղؽ�Ŀ
	const DYNAMIC_TYPE_LIKE=3;//ϲ�� ��Ŀ
	const DYNAMIC_TYPE_PUBLISH_PROGRAM=4;//������Ŀ
	const DYNAMIC_TYPE_SHARE=5;//�����Ŀ
	const DYNAMIC_TYPE_COMMENTS=6;//��������  
	const DYNAMIC_TYPE_COMMENT_REPLI=7;//�ظ�����  
	const DYNAMIC_TYPE_FOLLOW=8;//��ע ��
	const DYNAMIC_TYPE_UN_FOLLOW=9;//ȡ���ע�� 
	
	const DYNAMIC_TYPE_UN_FAVORITY=10; //ȡ���ղؽ�Ŀ
	const DYNAMIC_TYPE_LIKE_FRIEND=11;//ϲ�� REN
	
	const DYNAMIC_CONTENT_STATUS_APPROVAL=1; //
	const DYNAMIC_CONTENT_STATUS_DELETE=2;   //ɾ��
	
	const OBJECT_APPROVAL=1;
	const OBJECT_DELETE=-1;
	
	const NOTIFY_TYPE_FAVORITY=1;//�ղ�
	const NOTIFY_TYPE_SHARE=2; //����
	const NOTIFY_TYPE_COMMENT=3; //��������
	const NOTIFY_TYPE_FOLLOW=4;//��ע��
	const NOTIFY_TYPE_REPLIE_COMMENT=5;//�ظ��������
	const NOTIFY_TYPE_LIKE_PROGRAM=8;//ϲ��program
	const NOTIFY_TYPE_LIKE_PERSON=10;//ϲ��REN
	const NOTIFY_TYPE_WATCH_PROGRAM=9;//����
	const NOTIFY_TYPE_LIKE_FRIEND=11;//ϲ��REN
	
	const NOTIFY_TYPE_UN_FAVORITY=6;//ȡ���ղ�
	const NOTIFY_TYPE_UN_FOLLOW=7;//ȡ���ע
	
	const PROGRAM_TYPE_MOVIE=1; //��Ӱ 
	const PROGRAM_TYPE_TV=2;   //���� 
	const PROGRAM_TYPE_SHOW=3;  //���ս�Ŀ 
	
  }
?>