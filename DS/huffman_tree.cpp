#include <iostream>
#include <string>
#include <cstring>
using namespace std;

#define WT_ARRAY 800
const int max_w = 9999;

class HuffNode {
	public:
		int weight;
		//index
		int parent;
		int left;
		int right;

};


class HuffMan {
	private:
		void init();
		void find2MinNode(int pos,int *node1,int *node2);

	public:
		int node_num;
		int leaf_num;
		HuffNode *huffTree;
		string *huffCode;
		void init(int leaf,int * weight);
		void encode();
		void decode();
		~HuffMan() {
			node_num = 0;
			leaf_num = 0;
			delete []huffTree;
			delete []huffCode;
		};

};

//public
void HuffMan::init(int leaf,int* weight) {
	int i;
	leaf_num = leaf;
	node_num = 2*leaf_num-1;
	huffTree = new HuffNode[2*leaf_num];
	huffCode = new string[leaf_num+1];

	// set leaf weight
	for(i=1; i<=leaf_num; i++) {
		huffTree[i].weight = weight[i-1];//tree start from 1
	}


	for(i=1; i<=node_num; i++) {

		//set other node weight
		if(i>leaf_num) {
			huffTree[i].weight = 0; //not leaf
		}
		//init node params
		huffTree[i].parent = 0;
		huffTree[i].left = 0;
		huffTree[i].right = 0;
	}

	//connect left right
	init();
}

//private
void HuffMan::init() {
	int i,node1,node2;
	for(i=leaf_num+1; i<=node_num; i++) { // not leaf node
		find2MinNode(i-1,&node1,&node2);
		//node[i-1] is parennt of node1 and node2
	}
}


void HuffMan::find2MinNode(int pos,int *node1,int *node2) {
	int min,min_index = 1,i,t,w1,w2;

	min = huffTree[min_index].weight;
	//node1
	for(i = min_index+1; i<= pos; i++) {

		if(huffTree[i].weight < min && huffTree[i].parent == 0) {
			min_index = i;
			min = huffTree[i].weight;
		}
	}
	*node1 = min_index;

	//node2
	min_index = (*node1 == 1)? 2:1;
	min = huffTree[min_index].weight;
	for(i = min_index+1; i<= pos; i++) {

		if(i == *node1) {
			continue;
		}

		if(huffTree[i].weight < min && huffTree[i].parent == 0) {
			min_index = i;
			min = huffTree[i].weight;
		}
	}
	*node2 = min_index;

//	cout<<"node1 "<<*node1<<endl;
//	cout<<"node2 "<<*node2<<endl;
//	cout<<"pos+1 "<<pos+1<<endl;

	//connect leaf right
	huffTree[*node1].parent = pos+1;
	huffTree[*node2].parent = pos+1;

	w1 = huffTree[*node1].weight;
	w2 = huffTree[*node2].weight;

	huffTree[pos+1].left = (w1 <= w2) ? *node1 : *node2;
	huffTree[pos+1].right = (w1 > w2) ? *node1 : *node2;
	huffTree[pos+1].weight = w1+w2;

}


void HuffMan::encode() {
	int i,j,start,p;
	char *cd;
	cd = new char[leaf_num];

	cd[leaf_num-1]='\0';//end

	for(i=1; i<=leaf_num; i++) {
		start = leaf_num-1;

		for(j = i; huffTree[j].parent != 0; start--) {

			p = huffTree[j].parent;
		//	cout<<"h"<<j;
		//	cout<<":"<<huffTree[j].weight<<endl;
		//	cout<<"h[j]-p:"<<p<<endl;
		//	cout<<"huffTree[p].left("<<huffTree[p].left<<") == j("<<j<<")"<<endl;
			cd[start] = (huffTree[p].left == j)? '0':'1';
			j = huffTree[j].parent;
		}

		huffCode[i].assign(&cd[++start]);
//		cout<<"	huffCode[i]:"<<huffCode[i]<<endl;
	}

	delete []cd;

}

int main() {

	int t,n,i,j;
	int* wt;
	wt = new int[WT_ARRAY];
	HuffMan * huff;
	cin>>t;
	while(t--) {
		cin>>n;
		for(i=0; i<n; i++) {
			cin>>wt[i];
		}
		huff = new HuffMan();
		huff->init(n,wt);
		huff->encode();

		for(j=1; j<=n; j++) {
			cout<<huff->huffTree[j].weight<<'-';
			cout<<huff->huffCode[j]<<endl;
		}

		huff->~HuffMan();

	}


	return 0;
}


