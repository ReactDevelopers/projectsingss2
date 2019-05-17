<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;

    class Transactions extends Model{
        protected $table = 'transactions';
        protected $primaryKey = 'id_transaction';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        /**
         * [This method is for relating employer to transactions] 
         * @return Boolean
         */

        public function employers(){
            return $this->hasMany('\Models\Employers','transaction_user_id','id_user');
        }

        /**
         * [This method is for relating project to transactions] 
         * @return Boolean
         */

        public function project(){
            return $this->hasOne('\Models\Projects','id_project','transaction_project_id');
        }

        /**
         * [This method is for relating employer to transactions] 
         * @return Boolean
         */

        public function scopeDefaultKeys($query){
            $prefix         = \DB::getTablePrefix();
            
            $query->addSelect([
                'transactions.id_transactions',
                'transactions.transaction_user_id',
                'transactions.transaction_project_id',
                'transactions.transaction_proposal_id',
                'transactions.transaction_subtotal',
                'transactions.transaction_reference_id',
                'transactions.transaction_type',
                'transactions.transaction_status',
                'transactions.transaction_date',
                'transactions.created',
            ]);

            return $query;
        }

        /**
         * [This method is for scoping total paid by employer] 
         * @return Boolean
         */

        public function scopeTotalPaidByEmployer($query){
            $prefix         = \DB::getTablePrefix();

            $query->addSelect([
                \DB::Raw("
                    SUM(
                        `CONVERT_PRICE`({$prefix}transactions.transaction_subtotal, {$prefix}transactions.currency, '".request()->currency."')
                    ) as total_paid_by_employer
                "),
                \DB::Raw("'".___cache('currencies')[request()->currency]."' as price_unit")
            ]);

            return $query;
        }  

        /**
         * [This method is for scoping total paid by employer] 
         * @return Boolean
         */

        public function scopeBalance($query){
            $prefix         = \DB::getTablePrefix();

            # SUM(COALESCE(CASE WHEN transaction_type = 'debit' THEN transaction_subtotal END,0))
            $query->addSelect([
                \DB::raw("
                    `CONVERT_PRICE`((
                        SUM(transaction_subtotal) 
                    ), ".$prefix."transactions.currency, '".DEFAULT_CURRENCY."') as balance
                ")
            ]);

            return $query;
        }  
    }