<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\TblAccounts;
use App\TblAuthors;
use App\TblBarcodes;
use App\TblBorrowers;
use App\TblBooks;
use App\TblBounds;
use App\TblCategories;
use App\TblPublishers;

date_default_timezone_set('Asia/Manila');

class DashboardController extends Controller
{
    public function getIndex() {
        if(!session()->has('username')) {
            session()->flash('flash_status', 'danger');
            session()->flash('flash_message', 'Oops! Please login first.');

            return redirect()->route('cardinal.getIndex');
        } else {
            if(session()->get('type') != 'Librarian') {
                session()->flash('flash_status', 'danger');
                session()->flash('flash_message', 'Oops! You do not have to privilege to access the dashboard.');

                return redirect()->route('cardinal.getOpac');
            }
        }

        return view('dashboard.index');
    }

    public function getLoanBooks() {
        if(!session()->has('username')) {
            session()->flash('flash_status', 'danger');
            session()->flash('flash_message', 'Oops! Please login first.');

            return redirect()->route('cardinal.getIndex');
        } else {
            if(session()->get('type') != 'Librarian') {
                session()->flash('flash_status', 'danger');
                session()->flash('flash_message', 'Oops! You do not have to privilege to access the dashboard.');

                return redirect()->route('cardinal.getOpac');
            }
        }

        $data['bounds'] = TblBounds::join('tbl_authors', 'tbl_bounds.Author_ID', '=', 'tbl_authors.Author_ID')->get();
        $data['books'] = TblBooks::get();

        return view('dashboard.loan_books', $data);
    }

    public function getManageRecords($what) {
        if(!session()->has('username')) {
            session()->flash('flash_status', 'danger');
            session()->flash('flash_message', 'Oops! Please login first.');

            return redirect()->route('cardinal.getIndex');
        } else {
            if(session()->get('type') != 'Librarian') {
                session()->flash('flash_status', 'danger');
                session()->flash('flash_message', 'Oops! You do not have to privilege to access the dashboard.');

                return redirect()->route('cardinal.getOpac');
            }
        }

        switch($what) {
            case 'books':
                $data['bounds'] = TblBounds::join('tbl_authors', 'tbl_bounds.Author_ID', '=', 'tbl_authors.Author_ID')->get();
                $data['books'] = TblBooks::get();

                return view('dashboard.manage_records.books', $data);

                break;
            case 'authors':
                break;
            case 'publishers':
                break;
            case 'categories':
                break;
            case 'borrowers':
                $data['borrowers'] = TblAccounts::where('tbl_accounts.Type', '!=', 'Librarian')
                    ->leftJoin('tbl_borrowers', 'tbl_accounts.Owner_ID', '=', 'tbl_borrowers.Borrower_ID')
                ->get();

                return view('dashboard.manage_records.borrowers', $data);

                break;
            case 'librarians':
                $data['librarians'] = TblAccounts::where('Type', 'Librarian')->join('tbl_librarians', 'tbl_accounts.Owner_ID', '=', 'tbl_librarians.Librarian_ID')->get();

                return view('dashboard.manage_records.librarians', $data);

                break;
            default:
                break;
        }
    }

    public function getBarcodes($id) {
        if(!session()->has('username')) {
            session()->flash('flash_status', 'danger');
            session()->flash('flash_message', 'Oops! Please login first.');

            return redirect()->route('cardinal.getIndex');
        } else {
            if(session()->get('type') != 'Librarian') {
                session()->flash('flash_status', 'danger');
                session()->flash('flash_message', 'Oops! You do not have to privilege to access the dashboard.');

                return redirect()->route('cardinal.getOpac');
            }
        }

        $data['book'] = TblBooks::where('Book_ID', $id)->first();
        $data['barcodes'] = TblBarcodes::where('Book_ID', $id)->get();

        return view('dashboard.manage_records.barcodes', $data);
    }

    public function getAddRecord($what) {
        if(!session()->has('username')) {
            session()->flash('flash_status', 'danger');
            session()->flash('flash_message', 'Oops! Please login first.');

            return redirect()->route('cardinal.getIndex');
        } else {
            if(session()->get('type') != 'Librarian') {
                session()->flash('flash_status', 'danger');
                session()->flash('flash_message', 'Oops! You do not have to privilege to access the dashboard.');

                return redirect()->route('cardinal.getOpac');
            }
        }

        switch($what) {
            case 'books':
                $data['authors'] = TblAuthors::get();
                $data['books'] = TblBooks::get();
                $data['categories'] = TblCategories::get();
                $data['publishers'] = TblPublishers::get();

                return view('dashboard.manage_records.add_books', $data);

                break;
            case 'authors':
                break;
            case 'publishers':
                break;
            case 'categories':
                break;
            case 'borrowers':
                return view('dashboard.manage_records.add_borrowers');

                break;
            case 'librarians':
                return view('dashboard.manage_records.add_librarians');

                break;
            default:
                break;
        }
    }

    public function getEditRecord($what, $id) {
        if(!session()->has('username')) {
            session()->flash('flash_status', 'danger');
            session()->flash('flash_message', 'Oops! Please login first.');

            return redirect()->route('cardinal.getIndex');
        } else {
            if(session()->get('type') != 'Librarian') {
                session()->flash('flash_status', 'danger');
                session()->flash('flash_message', 'Oops! You do not have to privilege to access the dashboard.');

                return redirect()->route('cardinal.getOpac');
            }
        }

        $data['id'] = $id;

        switch($what) {
            case 'books':
                $data['authors'] = TblAuthors::get();
                $data['bounds'] = TblBounds::where('Book_ID', $id)->get();
                $data['book'] = TblBooks::where('Book_ID', $id)->first();
                $data['categories'] = TblCategories::get();
                $data['publishers'] = TblPublishers::get();

                return view('dashboard.manage_records.edit_books', $data);

                break;
            case 'authors':
                break;
            case 'publishers':
                break;
            case 'categories':
                break;
            case 'borrowers':
                $data['borrower'] = TblAccounts::where('tbl_accounts.Owner_ID', $id)->where('tbl_accounts.Type', '!=', 'Librarian')
                    ->leftJoin('tbl_borrowers', 'tbl_accounts.Owner_ID', '=', 'tbl_borrowers.Borrower_ID')
                ->first();
                    
                return view('dashboard.manage_records.edit_borrowers', $data);

                break;
            case 'librarians':
                $data['librarian'] = TblAccounts::where('tbl_accounts.Owner_ID', $id)->where('tbl_accounts.Type', 'Librarian')
                    ->leftJoin('tbl_librarians', 'tbl_accounts.Owner_ID', '=', 'tbl_librarians.Librarian_ID')
                ->first();

                return view('dashboard.manage_records.edit_librarians', $data);

                break;
            default:
                break;
        }
    }

    public function getDeleteRecord($what, $id) {
        if(!session()->has('username')) {
            session()->flash('flash_status', 'danger');
            session()->flash('flash_message', 'Oops! Please login first.');

            return redirect()->route('cardinal.getIndex');
        } else {
            if(session()->get('type') != 'Librarian') {
                session()->flash('flash_status', 'danger');
                session()->flash('flash_message', 'Oops! You do not have to privilege to access the dashboard.');

                return redirect()->route('cardinal.getOpac');
            }
        }

        $data['id'] = $id;

        switch($what) {
            case 'books':
                break;
            case 'authors':
                break;
            case 'publishers':
                break;
            case 'categories':
                break;
            case 'borrowers':
                $query1 = TblBorrowers::where('Borrower_ID', $id)->delete();
                $query2 = TblAccounts::where('Owner_ID', $id)->whereIn('Type', ['Student', 'Faculty'])->delete();

                if($query1 && $query2) {
                    session()->flash('flash_status', 'success');
                    session()->flash('flash_message', 'Borrower has been deleted.');
                } else {
                    session()->flash('flash_status', 'danger');
                    session()->flash('flash_message', 'Oops! Failed to delete borrower. Please refresh the page and try again.');
                }

                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            case 'librarians':
                $query1 = TblLibrarians::where('Librarian_ID', $id)->delete();
                $query2 = TblAccounts::where('Owner_ID', $id)->delete();

                if($query1 && $query2) {
                    session()->flash('flash_status', 'success');
                    session()->flash('flash_message', 'Borrower has been deleted.');
                } else {
                    session()->flash('flash_status', 'danger');
                    session()->flash('flash_message', 'Oops! Failed to delete borrower. Please refresh the page and try again.');
                }

                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            default:
                break;
        }
    }

    public function postAddBarcode(Request $request) {
        if(!session()->has('username')) {
            return response()->json(array('status' => 'Failed', 'message' => 'Oops! Please login first...'));
        } else {
            if(session()->get('type') != 'Librarian') {
                return response()->json(array('status' => 'Failed', 'message' => 'Oops! You do not have to privilege to access the dashboard.'));
            }
        }

        $addedBarcodes = 0;

        for($i = 0; $i < $request->input('numberOfCopies'); $i++) {
            $generatedBarcode = mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9);

            $query = TblBarcodes::insert([
                'Barcode_Number' => $generatedBarcode,
                'Book_ID' => $request->input('id')
            ]);

            if($query) {
                $addedBarcodes++;
            } else {
                $i--;
            }
        }

        if($addedBarcodes > 0) {
            return response()->json(array('status' => 'Success', 'message' => $addedBarcodes . ' barcode(s) has been added.'));
        } else {
            return response()->json(array('status' => 'Failed', 'message' => 'Oops! Failed to generate barcodes.'));
        }
    }

    public function postAddRecord($what, Request $request) {
        if(!session()->has('username')) {
            session()->flash('flash_status', 'danger');
            session()->flash('flash_message', 'Oops! Please login first.');

            return redirect()->route('cardinal.getIndex');
        } else {
            if(session()->get('type') != 'Librarian') {
                session()->flash('flash_status', 'danger');
                session()->flash('flash_message', 'Oops! You do not have to privilege to access the dashboard.');

                return redirect()->route('cardinal.getOpac');
            }
        }

        switch($what) {
            case 'books':
                $query = TblBooks::where('Title', $request->input('title'))->where('Edition', $request->input('edition'))->first();

                if($query) {
                    session()->flash('flash_status', 'danger');
                    session()->flash('flash_message', 'Oops! A book with the same title and edition number already exist.');
                } else {
                    $bookID = TblBooks::insertGetId([
                        'Title' => $request->input('title'),
                        'Edition' => $request->input('edition'),
                        'Collection_Type' => $request->input('collectionType'),
                        'Call_Number' => $request->input('callNumber'),
                        'ISBN' => $request->input('isbn'),
                        'Location' => $request->input('location'),
                        'Copyright_Year' => $request->input('copyrightYear'),
                        'Publisher_ID' => $request->input('publisher'),
                        'Category_ID' => $request->input('category')
                    ]);

                    if($bookID) {
                        $addedAuthors = 0;

                        for($i = 0; $i < $request->input('numberOfCopies'); $i++) {
                            $generatedBarcode = mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9);

                            $query = TblBarcodes::insert([
                                'Barcode_Number' => $generatedBarcode,
                                'Book_ID' => $bookID
                            ]);

                            if(!$query) {
                                $i--;
                            }
                        }

                        foreach(array_unique($request->input('authors')) as $author) {
                            $query = TblBounds::insert(array(
                                'Book_ID' => $bookID,
                                'Author_ID' => $author
                            ));

                            if($query) {
                                $addedAuthors++;
                            }
                        }

                        if($addedAuthors > 0) {
                            session()->flash('flash_status', 'success');
                            session()->flash('flash_message', 'Book has been added.');
                        } else {
                            session()->flash('flash_status', 'warning');
                            session()->flash('flash_message', 'Oops! Book has been added but failed to associate author(s).');
                        }
                    } else {
                        session()->flash('flash_status', 'danger');
                        session()->flash('flash_message', 'Oops! Failed to add book. Please refresh the page and try again.');
                    }
                }

                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            case 'authors':
                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            case 'publishers':
                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            case 'categories':
                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            case 'borrowers':
                $query = TblAccounts::where('Username', $request->input('borrowerID'))->first();

                if(!$query) {
                    $query = TblBorrowers::where('First_Name', $request->input('firstName'))->where('Last_Name', $request->input('lastName'))->first();

                    if(!$query) {
                        $borrowerID = TblBorrowers::insertGetId([
                            'First_Name' => $request->input('firstName'),
                            'Middle_Name' => $request->input('middleName'),
                            'Last_Name' => $request->input('lastName'),
                            'Birth_Date' => $request->input('birthDate'),
                            'Gender' => $request->input('gender')
                        ]);

                        if($borrowerID) {
                            $query = TblAccounts::insert([
                                'Username' => $request->input('borrowerID'),
                                'Password' => md5($request->input('birthDate')),
                                'Type' => $request->input('type'),
                                'Owner_ID' => $borrowerID
                            ]);

                            if($query) {
                                session()->flash('flash_status', 'success');
                                session()->flash('flash_message', 'Borrower has been added.');
                            } else {
                                session()->flash('flash_status', 'danger');
                                session()->flash('flash_message', 'Oops! Borrower has been added but failed to associate login account.');
                            }
                        } else {
                            session()->flash('flash_status', 'danger');
                            session()->flash('flash_message', 'Oops! Failed to add borrower. Please refresh the page and try again.');
                        }
                    } else {
                        session()->flash('flash_status', 'danger');
                        session()->flash('flash_message', 'Oops! Borrower already exist.');
                    }
                } else {
                    session()->flash('flash_status', 'danger');
                    session()->flash('flash_message', 'Oops! Borrower ID already in use by another person.');
                }

                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            default:
                break;
        }
    }

    public function postEditRecord($what, $id, Request $request) {
        if(!session()->has('username')) {
            session()->flash('flash_status', 'danger');
            session()->flash('flash_message', 'Oops! Please login first.');

            return redirect()->route('cardinal.getIndex');
        } else {
            if(session()->get('type') != 'Librarian') {
                session()->flash('flash_status', 'danger');
                session()->flash('flash_message', 'Oops! You do not have to privilege to access the dashboard.');

                return redirect()->route('cardinal.getOpac');
            }
        }

        switch($what) {
            case 'books':
                $query = TblBooks::where('Book_ID', $id)->first();

                if($query) {
                    $query = TblBounds::where('Book_ID', $id)->delete();

                    if($query) {
                        $addedAuthors = 0;

                        $updateBook = TblBooks::where('Book_ID', $id)->update([
                            'Title' => $request->input('title'),
                            'Edition' => $request->input('edition'),
                            'Collection_Type' => $request->input('collectionType'),
                            'Call_Number' => $request->input('callNumber'),
                            'ISBN' => $request->input('isbn'),
                            'Location' => $request->input('location'),
                            'Copyright_Year' => $request->input('copyrightYear'),
                            'Publisher_ID' => $request->input('publisher'),
                            'Category_ID' => $request->input('category')
                        ]);

                        foreach(array_unique($request->input('authors')) as $author) {
                            $query = TblBounds::insert(array(
                                'Book_ID' => $id,
                                'Author_ID' => $author
                            ));

                            if($query) {
                                $addedAuthors++;
                            }
                        }

                        if($addedAuthors > 0) {
                            session()->flash('flash_status', 'success');
                            session()->flash('flash_message', 'Book has been updated.');
                        } else {
                            session()->flash('flash_status', 'warning');
                            session()->flash('flash_message', 'Oops! Book has been updated but failed to associate author(s).');
                        }
                    } else {
                        session()->flash('flash_status', 'warning');
                        session()->flash('flash_message', 'Oops! Failed to update book. Please refresh the page and try again.');
                    }
                } else {
                    session()->flash('flash_status', 'danger');
                    session()->flash('flash_message', 'Oops! This book doesn\'t exist anymore.');
                }

                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            case 'authors':
                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            case 'publishers':
                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            case 'categories':
                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            case 'borrowers':
                /*
                    Possible Future Update(s):
                    => Check if name is already in the database
                */
                $query = TblAccounts::where('Owner_ID', $id)->first();

                if($query) {
                    $query = TblAccounts::where('Username', $request->input('borrowerID'))->first();

                    if(!$query || ($query && $query->Owner_ID == $id)) {
                        $query1 = TblAccounts::where('Owner_ID', $id)->where('Type', '!=', 'Librarian')->update([
                            'Username' => $request->input('borrowerID'),
                            'Type' => $request->input('type')
                        ]);

                        $query2 = TblBorrowers::where('Borrower_ID', $id)->update([
                            'First_Name' => $request->input('firstName'),
                            'Middle_Name' => $request->input('middleName'),
                            'Last_Name' => $request->input('lastName'),
                            'Birth_Date' => $request->input('birthDate'),
                            'Gender' => $request->input('gender')
                        ]);

                        if($query1 || $query2) {
                            session()->flash('flash_status', 'success');
                            session()->flash('flash_message', 'Borrower has been updated.');
                        } else {
                            session()->flash('flash_status', 'warning');
                            session()->flash('flash_message', 'No changes has been made.');
                        }
                    } else {
                        session()->flash('flash_status', 'danger');
                        session()->flash('flash_message', 'Oops! Borrower ID already in use by another person.');
                    }
                } else {
                    session()->flash('flash_status', 'danger');
                    session()->flash('flash_message', 'Oops! Borrower doesn\'t exist.');
                }
                
                return redirect()->route('dashboard.getManageRecords', $what);

                break;
            default:
                break;
        }
    }
}
