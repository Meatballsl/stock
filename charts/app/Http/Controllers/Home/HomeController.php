<?php

namespace App\Http\Controllers\Home;


use App\Models\ClosePrice;
use App\Models\Detail;

use App\Models\Financing;
use App\Models\HkIncome;
use App\Models\Income;
use App\Models\Incomek;
use App\Models\Stock;
use App\Models\Cate;
use App\Models\StockInCate;
use Illuminate\Http\Request;

class HomeController
{
//    //获取所有股票数据--old
//    public function stock()
//    {
//
//        $data = $this->getStockData();
//
//        return view("home.stock")
//            ->with('data', $data);
//
//    }

    //获取所有股票数据统计分类--new
    public function stockCate()
    {

        $data = $this->getStockCateData();
        return view("home.stockcate")
            ->with('data', $data);

    }

    //获取该分类下的所有股票数据--new
    public function stockInCate(Request $request)
    {

        $categoryId = $request->input('category_id');

        $data = $this->getStockInCateDate($categoryId);

        return view("home.stock")
            ->with('data', $data)->with('category_id', $categoryId);

    }

    public function category()
    {
        $data = $this->getCategoryData();
        return view("home.category")->with('data', $data);
    }

    //获取每只股票的具体详情 - 按照分类来，不同的股票，返回的不一样
    public function detail(Request $request)
    {

        $stockNumber = $request->input("stock_number");
        $cateIds = StockInCate::where('id', $stockNumber)->first();
        $cateId = $cateIds['category_id'];

        switch ($cateId) {
            case 1:
                //资金注入
                //显示收盘价和日资金净流入+累计资金净流入
                $date = [];
                $finance = [];
                $dayincome = [];
                $dayincomemount = [];
                $data = [];
                $financing = ClosePrice::where('stock_id', $stockNumber)->get();

                foreach ($financing as $key => $value) {
                    $date [] = $value->date;
                    $finance [] = $value->money;
                }

                $dayincomeAll = Income::where('stock_id', $stockNumber)->get();
                foreach ($dayincomeAll as $key => $value) {
                    $dayincome[] = $value->money;
                    $dayincomemount[] = $value->all_money;
                    $data [$key]['close'] = $financing[$key]->money;
                    $data [$key]['date'] = $value->date;
                    $data [$key]['income'] = $value->money;
                    $data [$key]['income_all'] = $value->all_money;
                    $data [$key]['id']= $value->id;
                }


                return view("home.closepriceincome")
                    ->with('date', implode(',', $date))
                    ->with('finance', implode(',', $finance))
                    ->with('dayincome', implode(',', $dayincome))
                    ->with('dayincomemount', implode(',', $dayincomemount))
                    ->with('categoryid', $cateId)
                    ->with('stocknumber', $stockNumber)
                    ->with('data', $data);
                break;
            case 2:

                //融资余额
                $date = [];
                $finance = [];
                $financing = Financing::where('stock_id', $stockNumber)->get();

                foreach ($financing as $key => $value) {
                    $date [] = $value->date;
                    $finance [] = $value->money;
                }

                return view("home.financing")
                    ->with('date', implode(',', $date))
                    ->with('finance', implode(',', $finance))
                    ->with('categoryid', $cateId)
                    ->with('stocknumber', $stockNumber)
                    ->with('data', $financing);
                break;
            case 3:
                //港股资金流入
                //融资余额
                $date = [];
                $finance = [];
                $financing = HkIncome::where('stock_id', $stockNumber)->get();

                foreach ($financing as $key => $value) {
                    $date [] = $value->date;
                    $finance [] = $value->money;
                }

                return view("home.hkincome")
                    ->with('date', implode(',', $date))
                    ->with('finance', implode(',', $finance))
                    ->with('categoryid', $cateId)
                    ->with('stocknumber', $stockNumber)
                    ->with('data', $financing);
                break;
            case 4:
                //日资金流入k线
                $date = [];
                $dayincome = [];
                $dayincomemount = [];

                $dayincomeAll = Incomek::where('stock_id', $stockNumber)->get();
                foreach ($dayincomeAll as $key => $value) {
                    $date [] = $value->date;
                    $dayincome[] = $value->money;
                    $dayincomemount[] = $value->all_money;
                }


                return view("home.incomek")
                    ->with('date', implode(',', $date))
                    ->with('dayincome', implode(',', $dayincome))
                    ->with('dayincomemount', implode(',', $dayincomemount))
                    ->with('categoryid', $cateId)
                    ->with('stocknumber', $stockNumber)
                    ->with('data', $dayincomeAll);
                break;
            default :
                return "不存在该分类";
        }

        $data = $this->getData($stockNumber);
        $date = [];
        $closing = [];
        $all = [];
        $financing = [];
        $all_hkincome = [];
        foreach ($data as $key => $value) {

            $date[] = $value->date;
            $all[] = $value->all_money;
            $closing[] = round($value->closing_price, 2);
            $financing[] = $value->financing;
            $all_hkincome[] = $value->all_hkincome;
        }
//dd($all_hkincome);

        return view("home.detail")
            ->with('date', implode(',', $date))
            ->with('all', implode(',', $all))
            ->with('closing', implode(',', $closing))
            ->with('data', $data)
            ->with('stocknumber', $stockNumber)
            ->with('financing', implode(',', $financing))
            ->with('all_hkincome', implode(',', $all_hkincome));
    }

    //获取每一只股票的数据
    public function getData($number)
    {

        $data = Detail::where('stock_number', $number)->get();
        return $data;


    }

    //获取所有股票数据
    public function getStockData()
    {

        $data = Stock::get();
        return $data;

    }

    //获取所有股票数据
    public function getStockCateData()
    {

        $data = Cate::get();
        return $data;

    }

    //getStockInCateDate
    //获取该分类下的所有股票
    public function getStockInCateDate($id)
    {

        $data = StockInCate::where('category_id', $id)->where('status',1)->get();
        return $data;

    }

    public function getCategoryData()
    {
        $data = Category::get();
        return $data;
    }

    //新增每只股票的数据
    public function add(Request $request)
    {
        $stocknumber = $request->input('stock_number');
        $categoryId = $request->input('category_id');

        switch ($categoryId) {
            case 1:
                //资金流入
                $date = $request->input('date');
                $close = $request->input('close');
                $income = $request->input('income');
                if ($date && $close && $income) {
                    $dateStr = preg_replace('/\s+/', ',', $date);
                    $closeStr = preg_replace('/\s+/', ',', $close);
                    $incomeStr = preg_replace('/\s+/', ',', $income);

                    $dateArr = explode(',', $dateStr);
                    $closeArr = explode(',', $closeStr);
                    $incomeArr = explode(',', $incomeStr);

                    if (count($dateArr) != count($closeArr) || count($incomeArr) != count($dateArr)) {
                        return "【日期】与【收盘价】与【日资金净流入】的数量不匹配";
                    } else {
                        foreach ($dateArr as $key => $value) {
                            $closeInsert[$key]['date'] = $value;
                            $closeInsert[$key]['stock_id'] = $stocknumber;
                            $closeInsert[$key]['money'] = $closeArr[$key];
                        }

                         ClosePrice::insert($closeInsert);

                        //处理加的
                        foreach ($incomeArr as $key => $value) {
                            $lastIncome = Income::where('stock_id', $stocknumber)->orderby('date', 'desc')->first();
                            $lastAllMoney = $lastIncome['all_money'];
                            if (!$lastAllMoney) {
                                $nowAllMoney = $value;
                            }else{
                                $nowAllMoney = $lastAllMoney+$value;
                            }
                            $incomeInsert = [
                                'date' => $dateArr[$key],
                                'stock_id' => $stocknumber,
                                'money' => $value,
                                'all_money' => $nowAllMoney,
                            ];
                            Income::insert($incomeInsert);

                        }


                    }
                } else return "缺少填写其中某项内容";

                break;
            case 2:
                //融资余额

                $date = $request->input('date');
                $money = $request->input('money');

                if ($date && $money) {
                    $dateStr = preg_replace('/\s+/', ',', $date);
                    $moneyStr = preg_replace('/\s+/', ',', $money);


                    $dateArr = explode(',', $dateStr);
                    $moneyArr = explode(',', $moneyStr);


                    if (count($dateArr) != count($moneyArr) ) {
                        return "【日期】与【融资余额】的数量不匹配";
                    } else {
                        foreach ($dateArr as $key => $value) {
                            $moneyInsert[$key]['date'] = $value;
                            $moneyInsert[$key]['stock_id'] = $stocknumber;
                            $moneyInsert[$key]['money'] = $moneyArr[$key];
                        }

                        Financing::insert($moneyInsert);

                    }
                } else return "缺少填写其中某项内容";
                break;
            case 3:
                //港股资金流入
                $date = $request->input('date');
                $money = $request->input('money');

                if ($date && $money) {
                    $dateStr = preg_replace('/\s+/', ',', $date);
                    $moneyStr = preg_replace('/\s+/', ',', $money);


                    $dateArr = explode(',', $dateStr);
                    $moneyArr = explode(',', $moneyStr);


                    if (count($dateArr) != count($moneyArr) ) {
                        return "【日期】与【港股资金流入】的数量不匹配";
                    } else {
                        foreach ($dateArr as $key => $value) {
                            $moneyInsert[$key]['date'] = $value;
                            $moneyInsert[$key]['stock_id'] = $stocknumber;
                            $moneyInsert[$key]['money'] = $moneyArr[$key];
                        }

                        HkIncome::insert($moneyInsert);

                    }
                } else return "缺少填写其中某项内容";
                break;
            case 4:
                //日资金流入k线
                $date = $request->input('date');
                $income = $request->input('income');
                if ($date  && $income) {
                    $dateStr = preg_replace('/\s+/', ',', $date);
                    $incomeStr = preg_replace('/\s+/', ',', $income);

                    $dateArr = explode(',', $dateStr);
                    $incomeArr = explode(',', $incomeStr);

                    if (count($incomeArr) != count($dateArr)) {
                        return "【日期】与【日资金净流入】的数量不匹配";
                    } else {

                        //处理加的
                        foreach ($incomeArr as $key => $value) {
                            $lastIncome = Incomek::where('stock_id', $stocknumber)->orderby('x_coordinate', 'desc')->first();
                            $lastAllMoney = $lastIncome['all_money'];
                            if (!$lastAllMoney) {
                                $nowAllMoney = $value;
                            }else{
                                $nowAllMoney = $lastAllMoney+$value;
                            }
                            $incomeInsert = [
                                'x_coordinate' => $dateArr[$key],
                                'stock_id' => $stocknumber,
                                'money' => $value,
                                'all_money' => $nowAllMoney,
                            ];
                            Incomek::insert($incomeInsert);

                        }


                    }
                } else return "缺少填写其中某项内容";

                break;
            default :
                return "不存在该分类";
        }





        return $this->detail($request);
    }

    //添加新分类下的新股票
    public function newstock(Request $request)
    {

        $name = $request->input('name');
        $categoryId = $request->input('category_id');
        if ($name && $categoryId) {
            StockInCate::insert([
                'name' => $name,
                'category_id' => $categoryId
            ]);

        }

        return $this->stockInCate($request);
    }

    //删除最后一条
    public function delete(Request $request)
    {
        $id = $request->input('id');
        $categoryId = $request->input('category_id');

        switch ($categoryId) {
            case 1:
                Income::destroy($id);
                ClosePrice::destroy($id);
                break;
            case 2:
                Financing::destroy($id);
                break;
            case 3:
                HkIncome::destroy($id);
                break;
            case 4:
                break;
        }

        return $this->detail($request);

    }

    //删除股票
    public function deleteStock(Request $request) {
        $id = $request->input('id');

        StockInCate::where('id',$id)->update(['status'=>-1]);
        return $this->stockInCate($request);
    }
}
